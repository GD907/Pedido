<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaccionesResource\Pages;
use App\Filament\Resources\TransaccionesResource\RelationManagers;
use App\Models\Transacciones;
use App\Models\Cajas;
use Filament\Forms;
use Filament\Resources\Form;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use App\Models\Cliente;
use App\Models\Producto;
use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};

class TransaccionesResource extends Resource
{
    protected static ?string $model = Transacciones::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Card::make()->schema([
                    // Fila 1
                    Forms\Components\DateTimePicker::make('fecha')
                        ->label('Fecha')
                        ->default(fn() => now())
                        ->disabled()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('numero_trx')
                        ->label('N° Transacción')
                        ->disabled()
                        ->default('TRX-' . random_int(100000, 999999))
                        ->columnSpan(1),

                    Forms\Components\Select::make('caja_id')
                        ->label('Caja')
                        ->required()
                        ->placeholder('Seleccione una caja disponible')
                        ->helperText('Si no se visualiza ninguna caja, asegúrese de abrir una caja.')
                        ->options($cajas = Cajas::where('estado', 'abierto')
                            ->where('users_id', Auth::id())
                            ->pluck('numero_caja', 'id'))
                        ->default($cajas->keys()->first())
                        ->preload()
                        ->columnSpan(1),

                    // Fila 2
                    Forms\Components\TextInput::make('observacion')
                        ->label('Observación')
                        ->maxLength(255)
                        ->columnSpan(3),

                    // Fila 3
                    Forms\Components\Select::make('clientes_id')
                        ->label('Cliente')
                        ->options(Cliente::all()->pluck('nombre_comercio', 'id'))
                        ->preload()
                        ->columnSpan(2),

                    Forms\Components\Select::make('metodo_pago')
                        ->label('Método de Pago')
                        ->required()
                        ->default('efectivo')
                        ->options([
                            'efectivo' => 'Efectivo',
                            'transferencia' => 'Transferencia',
                            'tarjeta' => 'Tarjeta',
                        ])
                        ->columnSpan(1),

                    // Hidden
                    Forms\Components\Hidden::make('users_id')
                        ->default(fn() => auth()->user()->id),

                    Forms\Components\Hidden::make('total_trx')
                        ->reactive(),

                    Placeholder::make('total2')
                        ->reactive()
                        ->label('Total (Gs)')
                        ->columnSpan(3)
                        ->extraAttributes(['class' => 'text-red-500 text-3xl', 'align' => 'right'])
                        ->content(function ($get, $set) {
                            $sum = 0;
                            foreach ($get('productos') as $product) {
                                if (is_numeric($product['precio']) && is_numeric($product['cantidad']) && is_numeric($product['pordescuento'])) {
                                    $precio = $product['precio'];
                                    $cantidad = $product['cantidad'];
                                    $pordescuento = $product['pordescuento'];

                                    $subtotal = $precio * $cantidad;
                                    $descuento = $subtotal * ($pordescuento / 100);
                                    $sum += $subtotal - $descuento;
                                }
                            }
                            $set('total_trx', $sum);
                            return number_format($sum, 0, '.', '.');
                        }),
                    Forms\Components\Select::make('porcentaje_descuento')
                        ->label('Descuento total (%)')
                        ->default(0)
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
                        ])
                        ->reactive()
                        ->columnSpan(1),

                    Forms\Components\Placeholder::make('total_con_descuento')
                        ->label('Total con descuento')
                        ->columnSpan(2)
                        ->extraAttributes(['class' => 'text-green-600 text-2xl', 'align' => 'right'])
                        ->reactive()
                        ->visible(fn ($get) => intval($get('porcentaje_descuento')) > 0)
                        ->content(function ($get, $set) {
                            $total = $get('total_trx') ?? 0;
                            $desc = $get('porcentaje_descuento') ?? 0;
                            $conDescuento = $total - ($total * ($desc / 100));
                            $set('total_con_descuento', $conDescuento);
                            return number_format($conDescuento, 0, '', '.') . ' Gs';
                        }),
                    Forms\Components\Hidden::make('total_con_descuento')->dehydrated(),
                ])->columns(3),

                Card::make()
                    ->columns(2)
                    ->schema([
                        Select::make('product_search')
                            ->label('Buscar producto por nombre')
                            ->placeholder('Seleccione un producto para ver su código')
                            ->options(Producto::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->reactive()
                            ->columnSpan(1),

                        Placeholder::make('search_code')
                            ->label('Código del Producto')
                            ->reactive()
                            ->content(function ($get, $set) {
                                $code = $get('product_search');
                                $set('search', Producto::find($code)?->codigo ?? 0);
                                return $get('search');
                            })
                            ->columnSpan(1),
                    ]),


                Section::make('Productos')
                    ->description('Agregar productos')
                    ->collapsible()
                    ->schema([
                        // products
                        Repeater::make('productos')
                            ->id('repeater-productos')
                            ->relationship()
                            ->schema([

                                Select::make('producto_id')
                                    ->label('Producto')
                                    ->placeholder('Ingrese el código de un producto')
                                    ->options(Producto::all()->pluck('codigo', 'id'))
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $producto = Producto::find($state);

                                        $set('precio', $producto?->precio_transacciones ?? 0);
                                        $set('cantidad', 1);
                                        $set('pordescuento', 0);

                                        // Calcular subtotal
                                        $subtotal = $producto?->precio_transacciones ?? 0;
                                        $set('subtotal', $subtotal);
                                    })
                                    ->columnSpan([
                                        'md' => 2,
                                    ]),

                                Hidden::make('searchprod')
                                    ->reactive(),
                                Placeholder::make('search_prod')
                                    ->reactive()
                                    ->label('Nombre del Producto')
                                    // ->columnSpan([
                                    //     'md' => 3,
                                    // ])

                                    // ->extraAttributes(['class' => 'text-red-500 text-3xl', 'align' => 'right'])
                                    ->content(function ($get, $set) {
                                        $code = $get('producto_id');
                                        $set('searchprod', Producto::find($code)?->nombre ?? 0);
                                        $code = $get('searchprod');
                                        return  $code;
                                    }),



                                Forms\Components\TextInput::make('cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->reactive()
                                    ->afterStateUpdated(
                                        fn($state, callable $set, callable $get) =>
                                        $set(
                                            'subtotal',
                                            ($state * ($get('precio') - ($get('precio') * ($get('pordescuento') / 100))))
                                        )
                                    ),
                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio Unitario (Gs)')
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
                                    ])
                                    ->afterStateUpdated(
                                        fn($state, callable $set, callable $get) =>
                                        $set(
                                            'subtotal',
                                            ($get('precio') * $get('cantidad')) - (($get('precio') * $state / 100) * $get('cantidad'))
                                        )
                                    ),
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
                                    ->required()
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
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->sortable()
                    ->formatStateUsing(
                        fn($state) =>
                        ucfirst(\Carbon\Carbon::parse($state)->translatedFormat('l d/m/Y - H:i'))
                    ),
                BadgeColumn::make('estado_transaccion')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'en curso',
                        'success' => 'cerrado',
                        'danger'  => 'Cancelado',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                TextColumn::make('numero_trx')
                    ->label('TRX N°')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('cajas.numero_caja')
                    ->label('Caja N°')
                    ->sortable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('metodo_pago')
                    ->label('Método de Pago')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                TextColumn::make('clientes.nombre_comercio')
                    ->label('Cliente')
                    ->sortable(),

                    TextColumn::make('total_venta_final')
                    ->label('Total Venta')
                    ->formatStateUsing(function ($state, $record) {
                        $monto = $record->total_con_descuento ?? $record->total_trx;
                        return number_format($monto, 0, '', '.') . ' Gs';
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->estado_transaccion === 'en curso'),

                Tables\Actions\Action::make('pdf')
                    ->label('Imprimir')
                    ->color('success')
                    ->icon('heroicon-s-printer')
                    ->action(function (Transacciones $record) {
                        $record->load('productos.producto');

                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('transaccion', ['record' => $record])
                            )
                                ->setPaper([0, 0, 198.45, 340.2], 'portrait')
                                ->stream();
                        }, $record->numero_trx . '.pdf');
                    }),

                Tables\Actions\Action::make('cancelarTransaccion')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(
                        fn($record) =>
                        $record->estado_transaccion === 'en curso' &&
                            auth()->user()->hasRole('super_admin')
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Transacción')
                    ->modalSubheading('¿Estás seguro de que quieres cancelar esta transacción? Esta acción no se puede deshacer.')
                    ->modalButton('Sí, Cancelar')
                    ->action(function ($record) {
                        $record->update(['estado_transaccion' => 'Cancelado']);

                        if ($record->caja_id) {
                            \App\Models\Cajas::where('id', $record->caja_id)->increment('contador_cancelados');
                        }
                    })
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListTransacciones::route('/'),
            'create' => Pages\CreateTransacciones::route('/create'),
            'view' => Pages\ViewTransacciones::route('/{record}'),
            'edit' => Pages\EditTransacciones::route('/{record}/edit'),
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
