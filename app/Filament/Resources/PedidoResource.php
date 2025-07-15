<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Filament\Resources\PedidoResource\RelationManagers;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Get;
use Closure;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use App\Models\Cliente;
use App\Models\Producto;
use Filament\Forms\Components\Select;
// Section
use Filament\Forms\Components\Section;
use Icetalker\FilamentStepper\Forms\Components\Stepper;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Facades\Blade;
use App\Models\{Cajas, Repartos};
class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Ventas';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Card::make()
                    ->schema([
                        Forms\Components\Hidden::make('users_id')
                            ->default(fn() => auth()->user()->id),

                        Forms\Components\TextInput::make('numero_factura')
                            ->disabled()
                            ->default('OR-' . random_int(100000, 999999)),

                        Forms\Components\DateTimePicker::make('fecha')
                            ->default(fn() => now())->disabled(),
                        // Forms\Components\Select::make('estado_pedidos_id')
                        //     ->relationship('estado_pedidos', 'nombre')->default('pendiente')->default(1)
                        //     ->label('Estado'),
                        Forms\Components\Select::make('clientes_id')
                            ->label('Cliente')
                            ->required()->options(Cliente::all()
                                ->pluck('nombre_comercio', 'id'))
                            ->preload()
                            ->columnSpan([
                                'md' => 3,
                            ])
                            ->searchable(),


                        Forms\Components\TextInput::make('observacion')
                            ->maxLength(255)
                            ->columnSpan([
                                'md' => 3,
                            ]),
                            Select::make('cobrado_por')
                            ->label('Cobrado por')
                            ->options([
                                'caja' => 'Caja',
                                'reparto' => 'Reparto',
                            ])
                            ->default('caja')
                            ->required()
                            ->reactive(), // <- importante para que condicione lo siguiente

                            Select::make('caja_id')
                            ->label('Caja')
                            ->options(fn () => \App\Models\Cajas::where('estado', 'abierto')
                                ->where('users_id', auth()->id())
                                ->pluck('numero_caja', 'id'))
                            ->default(fn () => \App\Models\Cajas::where('estado', 'abierto')
                                ->where('users_id', auth()->id())
                                ->pluck('id')
                                ->first())
                            ->searchable()
                            ->visible(fn ($get) => $get('cobrado_por') === 'caja')
                            ->preload()
                            ->placeholder('Seleccione una caja'),

                            Select::make('reparto_id')
                            ->label('Reparto asignado')
                            ->options(fn () => \App\Models\Repartos::pluck('zona', 'id'))
                            ->default(fn () => \App\Models\Repartos::pluck('id')->first())
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('cobrado_por') === 'reparto')
                            ->placeholder('Seleccione un reparto'),
                            Select::make('metodo_pago')
    ->label('Método de Pago')
    ->options([
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia',
        'tarjeta' => 'Tarjeta',
    ])
    ->default('efectivo')
    ->required()
    ->columnSpan(1),
                        Hidden::make('total_venta') //Quiero guardar en este la suma de todas las ventas
                            ->reactive(),
                        // ->hidden(),
                        Placeholder::make('total_venta2')
                            ->reactive()
                            ->label('Total (Gs)')
                            ->columnSpan([
                                'md' => 3,
                            ])

                            ->extraAttributes(['class' => 'text-red-500 text-3xl', 'align' => 'right'])
                            ->content(function ($get, $set) {
                                $sum = 0;
                                foreach ($get('productos') as $product) {
                                    // Verificar que 'precio', 'cantidad' y 'pordescuento' sean numéricos
                                    if (is_numeric($product['precio']) && is_numeric($product['cantidad']) && is_numeric($product['pordescuento'])) {
                                        $precio = $product['precio'];
                                        $cantidad = $product['cantidad'];
                                        $pordescuento = $product['pordescuento'];

                                        $subtotal = $precio * $cantidad;
                                        $descuento = $subtotal * ($pordescuento / 100);
                                        $sum += $subtotal - $descuento;
                                    } else {
                                        // Aquí simplemente omitimos el producto no válido
                                        continue;
                                    }
                                }
                                $set('total_venta', $sum);
                                $sum = number_format($sum, 0, '.', '.');
                                return $sum;
                            }),


                    ]),

                Section::make('Productos')
                    ->description('Agregar productos')
                    ->collapsible()
                    ->schema([
                        // products
                        Repeater::make('productos')
                            ->relationship()
                            ->schema([

                                Select::make('producto_id')
                                    ->label('Producto')
                                    ->options(Producto::all()->pluck('nombre', 'id'))

                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $set('precio', Producto::find($state)?->precio ?? 0);
                                        $set('subtotal', Producto::find($state)?->precio ?? 0);
                                    })
                                    ->columnSpan([
                                        'md' => 2,
                                    ]),
                                // Hidden::make('disponible')
                                //     ->reactive(),
                                // Placeholder::make('disponible2')
                                //     ->reactive()
                                //     ->label('Disponible')
                                //     // ->columnSpan([
                                //     //     'md' => 3,
                                //     // ])

                                //     // ->extraAttributes(['class' => 'text-red-500 text-3xl', 'align' => 'right'])
                                //     ->content(function ($get, $set) {
                                //         $cant = $get('producto_id');
                                //         $cant = intval($cant);
                                //         $set('disponible', Producto::find($cant)?->stock ?? 0);
                                //         $cant = $get('disponible');
                                //         return  $cant;
                                //     }),

                                Forms\Components\TextInput::make('cantidad')
                                    ->minValue(0)
                                    ->maxValue(10000)
                                    ->numeric()
                                    ->default(1)
                                    ->reactive()
                                    // ->rules([
                                    //     fn($get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                    //         $dis = $get('disponible');

                                    //         if ($value > $get('disponible')) {
                                    //             $fail("Cantidad no disponible. Stock actual: {$dis} unidades.");
                                    //         }
                                    //     },
                                    // ])
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                    $set(
                                        'subtotal',
                                        ($state * ($get('precio') - ($get('precio') * ($get('pordescuento') / 100)))),
                                    ))
                                    ->columnSpan([
                                        'md' => 1,
                                    ]),
                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio Unitario (Gs)')
                                    ->reactive()

                                    // ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                                    // $set('subtotal', $state * $get('cantidad')))
                                    ->numeric()
                                    ->disabled()

                                    ->columnSpan([
                                        'md' => 2,
                                    ]),
                                Select::make('pordescuento')
                                    ->label('Descuento')
                                    ->default(0)
                                    ->reactive()
                                    ->options([
                                        0 => '0%',
                                        1 => '1%',
                                        2 => '2%',
                                        3 => '3%',
                                        4 => '4%',
                                        5 => '5%',
                                        6 => '6%',
                                        7 => '7%',
                                        8 => '8%',
                                        9 => '9%',
                                        10 => '10%',
                                        11 => '11%',
                                        12 => '12%',
                                        13 => '13%',
                                        14 => '14%',
                                        15 => '15%',
                                        100 => '100 %',
                                    ])
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                    $set(
                                        'subtotal',
                                        ($get('precio') * $get('cantidad')) - (($get('precio') * ($state) / 100)) * $get('cantidad'),
                                    )),
                                // Forms\Components\TextInput::make('descuento')
                                // ->label('Descuento')
                                // ->reactive()
                                // ->postfix('Gs')
                                // ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                                // $set(
                                //     'subtotal',
                                //     ($get('cantidad') * $get('precio')),
                                // ))
                                // ->numeric()
                                // ->disabled()
                                // ->required()
                                // ->columnSpan([
                                //     'md' => 2,
                                // ]),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal (Gs)')
                                    ->disabled()
                                    ->numeric()
                                    ->reactive()

                                    ->postfix('Gs')
                                    ->columnSpan([
                                        'md' => 2,
                                    ]),
                            ])
                            // ->afterStateUpdated(function ($state, callable $set) {
                            //     $total_venta = 0;
                            //     foreach ($this->state('productos') as $producto) {
                            //         $total_venta += $producto['subtotal'];
                            //     }
                            //     $this->state(['total_venta' => $total_venta]);
                            // })
                            ->orderable()
                            ->defaultItems(1)
                            ->disableLabel()
                            ->columns([
                                'md' => 10,
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->defaultSort('fecha', 'desc')
        ->columns([
            BadgeColumn::make('estado_pedido')
            ->label('Estado')
            ->searchable()
            ->sortable()
            ->color(fn ($state) => match (strtolower($state)) {
                'pendiente' => 'secondary',
                'cancelado' => 'danger',
                'entregado', 'cerrado' => 'success',
                default => 'secondary',
            })
            ->formatStateUsing(fn ($state) => ucfirst(strtolower($state))),

            Tables\Columns\TextColumn::make('fecha')
                ->label('Fecha')
                ->sortable()
                ->formatStateUsing(fn ($state) =>
                    ucfirst(\Carbon\Carbon::parse($state)->translatedFormat('l d/m/Y - H:i'))
                ),

            Tables\Columns\TextColumn::make('clientes.nombre_comercio')
                ->label('Cliente')
                ->searchable()
                ->sortable(),

                Tables\Columns\BadgeColumn::make('cobrado_por')
                ->label('Cobrado por')
                ->colors([
                    'info' => 'caja',
                    'secondary' => 'reparto',
                ])
                ->formatStateUsing(fn ($state) => ucfirst($state ?? 'No especificado')),

                Tables\Columns\TextColumn::make('caja.numero_caja')
                ->label('Caja N°')
                ->sortable()
                ->formatStateUsing(fn ($state, $record) =>
                    ($record?->cobrado_por === 'caja') ? $state : '-'
                ),
                Tables\Columns\TextColumn::make('reparto.zona')
                ->label('Zona de Reparto')
                ->sortable()
                ->searchable()
                ->formatStateUsing(fn ($state, $record) =>
                    $record?->cobrado_por === 'reparto' ? $state : '-'
                ),

            Tables\Columns\TextColumn::make('total_venta')
                ->label('Total Venta')
                ->formatStateUsing(fn ($state) =>
                    number_format($state, 0, '', '.') . ' Gs'
                ),
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make(),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),

            Tables\Actions\EditAction::make()
            ->visible(fn ($record) => !in_array($record->estado_pedido, ['Cancelado', 'cerrado'])),
            Tables\Actions\Action::make('pdf')
                ->label('Imprimir')
                ->color('success')
                ->icon('heroicon-s-printer')
                ->action(function (Pedido $record) {
                    $record->load('productos.producto');

                    return response()->streamDownload(function () use ($record) {
                        echo Pdf::loadHtml(
                            Blade::render('factura', ['record' => $record])
                        )->setPaper([0, 0, 595.35, 340.2])
                         ->stream();
                    }, $record->numero_factura . '.pdf');
                }),

            // Tables\Actions\Action::make('procesar_pedido')
            //     ->label('Procesar')
            //     ->color('primary')
            //     ->icon('heroicon-s-save')
            //     ->requiresConfirmation()
            //     ->modalHeading('Confirmar entrega')
            //     ->modalSubheading('¿Estás seguro de que este pedido ha sido entregado? Esta acción no se puede deshacer.')
            //     ->modalButton('Sí, pedido entregado')
            //     ->visible(fn ($record) => !in_array($record->estado_pedido, ['Cancelado', 'Entregado']))
            //     ->action(function (Pedido $record) {
            //         $record->update(['estado_pedido' => 'Entregado']);
            //         $record->load('productos.producto');
            //         foreach ($record->productos as $detalle) {
            //             $detalle->producto?->decrement('stock', $detalle->cantidad);
            //         }
            //     }),

                Tables\Actions\Action::make('cancelar')
                ->label('Cancelar')
                ->color('danger')
                ->icon('heroicon-s-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Confirmar cancelación')
                ->modalSubheading('¿Estás seguro de que deseas cancelar este pedido? Esta acción no se puede deshacer.')
                ->modalButton('Sí, cancelar pedido')
                ->visible(fn ($record) => $record->estado_pedido !== 'Cancelado' && $record->estado_pedido !== 'Entregado' && $record->estado_pedido !== 'cerrado')
                ->action(fn (Pedido $record) =>
                    $record->update(['estado_pedido' => 'Cancelado'])
                ),
        ])
        ->bulkActions([
            Tables\Actions\RestoreBulkAction::make(),
            FilamentExportBulkAction::make('export'),
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'view' => Pages\ViewPedido::route('/{record}'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
