<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntradaResource\Pages;
use App\Filament\Resources\EntradaResource\RelationManagers;
use App\Models\Entrada;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use App\Models\Producto;
use Icetalker\FilamentStepper\Forms\Components\Stepper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
class EntradaResource extends Resource
{
    protected static ?string $model = Entrada::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                //

                Forms\Components\DateTimePicker::make('fecha')
                    ->default(fn() => now())->disabled(),
                Forms\Components\Hidden::make('users_id')
                    ->default(fn() => auth()->user()->id),
                Select::make('tipo_entrada')
                    ->label('Tipo de Entrada')
                    ->options([
                        'Compra' => 'Compra',
                        'Devolucion' => 'Devolución',
                    ])
                    ->default('Compra')
                    ->required(),
                Forms\Components\TextInput::make('proveedor')
                    ->maxLength(150)
                    ->label('Proveedor'),
                Forms\Components\TextInput::make('observacion')
                    ->maxLength(255)
                    ->columnSpan([
                        'md' => 2,
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
                                    ->required()
                                    ->searchable()
                                    ->reactive()
                                    ->columnSpan([
                                        'md' => 3,
                                    ]),

                                Stepper::make('cantidad')
                                    ->minValue(0)
                                    ->maxValue(500)
                                    ->default(1)
                                    ->reactive()
                                    ->columnSpan([
                                        'md' => 1,
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('preciocompra')
                                    ->label('Precio Compra')
                                    ->reactive()
                                    ->numeric()


                                    ->columnSpan([
                                        'md' => 2,
                                    ]),

                                Forms\Components\TextInput::make('precioventa')
                                    ->label('Precio Venta Pedidos')
                                    ->numeric()
                                    ->reactive()
                                    ->postfix('Gs')
                                    ->columnSpan([
                                        'md' => 2,
                                    ]),
                                Forms\Components\TextInput::make('preciotransaccion')
                                    ->label('Precio Venta Transacciones')
                                    ->numeric()
                                    ->reactive()
                                    ->postfix('Gs')
                                    ->columnSpan([
                                        'md' => 2,
                                    ]),
                            ])
                            ->orderable()
                            ->defaultItems(1)
                            ->disableLabel()
                            ->columns([
                                'md' => 10,
                            ])
                            ->required(),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('fecha', 'desc') // Ordena por la columna de fecha en orden descendente
            ->columns([
                Tables\Columns\TextColumn::make('fecha')->label('Fecha de Entrada')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('proveedor')->label('Proveedor')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tipo_entrada')->label('Tipo')->sortable(),
                Tables\Columns\TextColumn::make('fue_procesado')->label('Procesado')
                ->sortable()
                ->formatStateUsing(fn($state) => $state === 1 ? 'Sí' : 'No') // Mostrar "Sí" o "No"
                ->color(fn($record) => $record->fue_procesado === 1 ? 'success' : 'danger') // 'success' para verde, 'danger' para rojo

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('procesar')
                ->label('Procesar')
                ->icon('heroicon-o-save') // Ícono de guardar para el botón "Procesar"
                ->action(function (Entrada $record) {
                    foreach ($record->productos as $detalle) {
                        $producto = $detalle->producto;

                        // Sumar la cantidad al stock actual
                        $producto->stock += $detalle->cantidad;

                        // Actualizar preciocompra si no es null
                        if (!is_null($detalle->preciocompra)) {
                            $producto->preciocompra = $detalle->preciocompra;
                        }

                        // Actualizar precioventa si no es null
                        if (!is_null($detalle->precioventa)) {
                            $producto->precio = $detalle->precioventa;
                        }

                        // Actualizar preciotransaccion si no es null
                        if (!is_null($detalle->preciotransaccion)) {
                            $producto->precio_transacciones = $detalle->preciotransaccion;
                        }

                        // Guardar los cambios en el producto
                        $producto->save();
                    }

                    // Actualizar el campo fue_procesado a 1 después de procesar
                    $record->fue_procesado = 1;
                    $record->save();
                })
                ->requiresConfirmation()
                ->modalHeading('Confirmar entrada')
                ->modalSubheading('¿Estás seguro de actualizar el stock? Esta acción no se puede deshacer.')
                ->modalButton('Sí, actualizar')
                ->visible(fn (Entrada $record) => $record->fue_procesado === 0), // Condición de visibilidad
                Tables\Actions\Action::make('pdf')
                ->label('Imprimir')
                ->color('success')
                ->icon('heroicon-s-printer')
                ->action(function (Entrada $record) {
                    $record->load('productos.producto');
                    // dd($record->productos);
                    return response()->streamDownload(function () use ($record) {
                        echo Pdf::loadHtml(
                            Blade::render('entrada', ['record' => $record])
                        ) ->setPaper([0, 0, 595.35, 340.2])
                        ->stream();
                    }, $record->fecha . '.pdf');
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
            'index' => Pages\ListEntradas::route('/'),
            'create' => Pages\CreateEntrada::route('/create'),
            'edit' => Pages\EditEntrada::route('/{record}/edit'),
        ];
    }
}
