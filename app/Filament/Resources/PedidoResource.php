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
use Illuminate\Support\Facades\Blade;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

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
                        Forms\Components\Select::make('estado_pedidos_id')
                            ->relationship('estado_pedidos', 'nombre')->default('pendiente')->default(1)
                            ->label('Estado'),
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
                                Hidden::make('disponible')
                                    ->reactive(),
                                Placeholder::make('disponible2')
                                    ->reactive()
                                    ->label('Disponible')
                                    // ->columnSpan([
                                    //     'md' => 3,
                                    // ])

                                    // ->extraAttributes(['class' => 'text-red-500 text-3xl', 'align' => 'right'])
                                    ->content(function ($get, $set) {
                                        $cant = $get('producto_id');
                                        $cant = intval($cant);
                                        $set('disponible', Producto::find($cant)?->stock ?? 0);
                                        $cant = $get('disponible');
                                        return  $cant;
                                    }),

                                Forms\Components\TextInput::make('cantidad')
                                    ->minValue(0)
                                    ->maxValue(10000)
                                    ->numeric()
                                    ->default(1)
                                    ->reactive()
                                    ->rules([
                                        fn($get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                            $dis = $get('disponible');

                                            if ($value > $get('disponible')) {
                                                $fail("Cantidad no disponible. Stock actual: {$dis} unidades.");
                                            }
                                        },
                                    ])
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
        ->defaultSort('fecha', 'desc') // Ordena por la columna de fecha en orden descendente
            ->columns([
                Tables\Columns\TextColumn::make('numero_factura')
                    ->label('N°')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('estado_pedidos.nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Estado')
                    ->color(function ($record) {
                        return match ($record->estado_pedidos_id) {
                            1 => 'secondary', // Fondo amarillo para estado 1
                            2 => 'danger',  // Fondo rojo para estado 2
                            3 => 'success', // Fondo verde para estado 3
                            default => 'secondary', // Otro color para otros estados
                        };
                    }),

                Tables\Columns\TextColumn::make('fecha')
                    ->sortable()
                    ->date()
                    ->label('Fecha'),
                Tables\Columns\TextColumn::make('clientes.nombre_comercio')
                    ->searchable()
                    ->sortable()
                    ->label('Cliente'),
                Tables\Columns\TextColumn::make('total_venta')->label('Total Venta (Gs)'),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                ->visible(fn ($record) => !in_array($record->estado_pedidos_id, [2, 3])),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('Imprimir')
                    ->color('success')
                    ->icon('heroicon-s-printer')
                    ->action(function (Pedido $record) {
                        // Cargar la relación 'pedidoDetalles' y la relación 'producto' dentro de 'pedidoDetalles'

                        $record->load('productos.producto');
                        // dd($record->productos);
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('factura', ['record' => $record])
                            )->stream();
                        }, $record->numero_factura . '.pdf');
                    }),
                    Tables\Actions\Action::make('procesar_pedido')
    ->label('Procesar')
    ->color('primary')
    ->icon('heroicon-s-check')
    ->requiresConfirmation()
    ->modalHeading('Confirmar cancelación')
    ->modalSubheading('¿Estás seguro de que este pedido ha sido entregado? Esta acción no se puede deshacer.')
    ->modalButton('Sí, pedido entregado')
    ->visible(fn ($record) => !in_array($record->estado_pedidos_id, [2, 3]))
    ->action(function (Pedido $record) {
        // Cambiar el estado del pedido a '3' (procesado)
        $record->update(['estado_pedidos_id' => 3]);

        // Cargar la relación de productos en pedidoDetalles
        $record->load('productos.producto');

        // Procesar el stock de cada producto
        foreach ($record->productos as $detalle) {
            $producto = $detalle->producto;

            if ($producto) {
                // Resta la cantidad vendida del stock actual
                $producto->decrement('stock', $detalle->cantidad);
            }
        }
    }),
    Tables\Actions\Action::make('cancelar')
    ->label('Cancelar')
    ->color('danger')
    ->icon('heroicon-s-x-circle')
    ->visible(fn ($record) => !in_array($record->estado_pedidos_id, [2, 3]))
    ->requiresConfirmation()
    ->modalHeading('Confirmar cancelación')
    ->modalSubheading('¿Estás seguro de que deseas cancelar este pedido? Esta acción no se puede deshacer.')
    ->modalButton('Sí, cancelar pedido')
    ->action(function (Pedido $record) {
        $record->update(['estado_pedidos_id' => 2]);

    })



            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                FilamentExportBulkAction::make('export')
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
