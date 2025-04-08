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
                Card::make()
                    ->schema([
                        // ...
                        Forms\Components\Hidden::make('users_id')
                            ->default(fn() => auth()->user()->id),
                        Forms\Components\DateTimePicker::make('fecha')
                            ->default(fn() => now())->disabled(),
                        Forms\Components\TextInput::make('numero_trx')
                            ->disabled()
                            ->default('TRX-' . random_int(100000, 999999)),
                        Forms\Components\TextInput::make('observacion')
                            ->maxLength(255)
                            ->columnSpan([
                                'md' => 2,
                            ]),
                        Forms\Components\Select::make('clientes_id')
                            ->label('Cliente')
                            ->options(Cliente::all()
                                ->pluck('nombre_comercio', 'id'))
                            ->preload()
                            ->columnSpan([
                                'md' => 3,
                            ]),
                            Forms\Components\Select::make('caja_id')
                            ->label('Caja')
                            ->required()
                            ->placeholder('Seleccione una caja disponible')
                            ->helperText('Si no se visualiza ninguna caja, asegúrese de abrir una caja.')
                            ->options($cajas = Cajas::where('estado', 'abierto') // Filtrar por estado 'abierto'
                                ->where('users_id', Auth::id()) // Filtrar por el usuario autenticado
                                ->pluck('numero_caja', 'id'))
                            ->default($cajas->keys()->first()) // Seleccionar por defecto el primer id
                            ->preload()
                            ->columnSpan([
                                'md' => 3,
                            ]),


                        Hidden::make('total_trx') //Quiero guardar en este la suma de todas las ventas
                            ->reactive(),
                        // ->hidden(),
                        Placeholder::make('total2')
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
                                $set('total_trx', $sum);
                                $sum = number_format($sum, 0, '.', '.');
                                return $sum;
                            }),

                    ]),
                    Card::make()
                    ->schema([
                        Select::make('product_search')
                        ->label('Buscar producto por nombre')
                        ->placeholder('Seleccione un producto para ver su código')
                        ->options(Producto::all()->pluck('nombre', 'id'))
                        ->searchable()
                        ->reactive()
                        ->columnSpan([
                            'md' => 2,
                        ]),
                        Hidden::make('search')
                        ->reactive(),
                    Placeholder::make('search_code')
                        ->reactive()
                        ->label('Codigo del Producto')
                        // ->columnSpan([
                        //     'md' => 3,
                        // ])

                        // ->extraAttributes(['class' => 'text-red-500 text-3xl', 'align' => 'right'])
                        ->content(function ($get, $set) {
                            $code = $get('product_search');
                            $set('search', Producto::find($code)?->codigo ?? 0);
                            $code = $get('search');
                            return  $code;
                        }),
                    ])
                    ->columns(2),



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
                                    ->placeholder('Ingrese el código de un producto')
                                    ->options(Producto::all()->pluck('codigo', 'id'))
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $set('precio', Producto::find($state)?->precio_transacciones ?? 0);
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
                                    ->minValue(0)
                                    ->maxValue(10000)
                                    ->numeric()
                                    ->default(1)
                                    ->reactive()
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
        ->defaultSort('fecha', 'desc') // Ordena por la columna de fecha en orden descendente
            ->columns([
                //
                Tables\Columns\TextColumn::make('numero_trx')->label('TRX N°')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('cajas.numero_caja')->label('Caja N°')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                ->sortable()
                ->label('Fecha'),
                Tables\Columns\TextColumn::make('estado_transaccion')->label('Estado')
                ->sortable()
                ->color(function ($record) {
                    return match ($record->estado_transaccion) {
                        'Procesado' => 'success',  // Fondo verde para estado 'Procesado'
                        'Cancelado' => 'danger',  // Fondo verde para estado 'Procesado'
                        'en curso' => 'secondary',   // Fondo gris/azul para estado 'en curso'
                        default => 'secondary',         // Fondo rojo para otros estados
                    };
                })
                ->searchable(),
                Tables\Columns\TextColumn::make('clientes.nombre_comercio')->label('Cliente')
                ->sortable(),
                Tables\Columns\TextColumn::make('total_trx')
                ->label('Total Venta')
                ->formatStateUsing(function ($state) {
                    // Divide por 100 si el valor original incluye centavos y luego formatea sin decimales
                    $formattedValue = number_format($state, 0, '', '.');
                    return $formattedValue . ' Gs';
                })
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                ->visible(fn ($record) => $record->estado_transaccion === 'en curso'),
                Tables\Actions\Action::make('pdf')
                ->label('Imprimir')
                ->color('success')
                ->icon('heroicon-s-printer')
                ->action(function (Transacciones $record) {
                    // Cargar la relación 'productos' y la relación 'producto' dentro de 'productos'
                    $record->load('productos.producto');

                    return response()->streamDownload(function () use ($record) {
                        echo Pdf::loadHtml(
                            Blade::render('transaccion', ['record' => $record])
                        )
                        ->setPaper([0, 0, 198.45, 340.2], 'portrait')  // Ancho 80 mm en puntos, ajusta la altura según el contenido

                        // Ajustar altura para más espacio vertical
                        ->stream();
                    }, $record->numero_trx . '.pdf');
                }),
                Tables\Actions\Action::make('cancelarTransaccion')
                ->label('Cancelar')
                ->icon('heroicon-o-x-circle')
                ->color('danger') // Cambia el color del botón para resaltar la acción de cancelación
                ->visible(fn ($record) => $record->estado_transaccion === 'en curso') // Visible solo si está en curso
                ->requiresConfirmation() // Muestra una confirmación antes de ejecutar la acción
                ->modalHeading('Cancelar Transacción')
                ->modalSubheading('¿Estás seguro de que quieres cancelar esta transacción? Esta acción no se puede deshacer.')
                ->modalButton('Sí, Cancelar')
                ->action(function ($record) {
                    // Cambiamos el estado a "Cancelado"
                    $record->update([
                        'estado_transaccion' => 'Cancelado',
                    ]);

                    // Incrementamos el contador_cancelados en la tabla cajas
                    $cajaId = $record->caja_id; // Asegúrate de que `caja_id` esté en el registro de la transacción
                    if ($cajaId) {
                        \App\Models\Cajas::where('id', $cajaId)->increment('contador_cancelados');
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
