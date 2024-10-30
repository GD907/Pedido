<?php

namespace App\Filament\Resources;

use App\Models\Transacciones;
use App\Filament\Resources\CajasResource\Pages;
use App\Filament\Resources\CajasResource\RelationManagers;
use App\Models\Cajas;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Illuminate\Support\Carbon;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;

class CajasResource extends Resource
{
    protected static ?string $model = Cajas::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Card::make()
                    ->schema([
                        // ...
                        Forms\Components\DateTimePicker::make('fecha')
                            ->default(fn() => now())->disabled(),
                        Forms\Components\TextInput::make('numero_caja')
                            ->disabled()
                            ->default('TR-' . random_int(100000, 999999)),
                        Forms\Components\TextInput::make('observacion')
                            ->maxLength(255)
                            ->columnSpan([
                                'md' => 3,
                            ]),
                        Forms\Components\Hidden::make('users_id')
                            ->default(fn() => auth()->user()->id),



                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('fecha', 'desc') // Ordena por la columna de fecha en orden descendente
            ->columns([
                //
                Tables\Columns\TextColumn::make('numero_caja')->label('N°')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->sortable()
                    ->label('Fecha'),
                Tables\Columns\TextColumn::make('users.name')
                    ->sortable()
                    ->label('Encargado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')->label('Estado')
                    ->searchable()
                    ->sortable()
                    ->color(fn($record) => $record->estado === 'abierto' ? 'success' : 'danger') // 'success' para verde, 'danger' para rojo


            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cerrarCaja')
                    ->label('Cerrar Caja')
                    ->visible(fn($record) => $record->estado === 'abierto' )
                    ->action(function (Cajas $record) {
                        // Contamos las transacciones relacionadas con la caja en curso
                        $transaccionesEnCurso = Transacciones::where('caja_id', $record->id)
                        ->where('estado_transaccion', 'en curso')
                        ->where('estado_transaccion', '!=', 'Cancelado') // Excluye las transacciones canceladas
                        ->get();

                        $totalTransacciones = $transaccionesEnCurso->count();
                        $sumaTotalTransacciones = $transaccionesEnCurso->sum('total_trx');
                        $record->update([
                            'estado' => 'cerrado',
                            'cierre' => Carbon::now(), // Fecha y hora actual
                            'cantidad_trx' => $totalTransacciones, // Guardamos el total de transacciones
                            'total_caja' => $sumaTotalTransacciones, // Guardamos la suma de las transacciones
                        ]);
                        // Actualizamos el estado de transacción a "Cerrado"
                        Transacciones::where('caja_id', $record->id)
                        ->where('estado_transaccion', 'en curso') // Actualiza solo las que están en curso
                        ->update(['estado_transaccion' => 'Cerrado']);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Cierre de Caja')
                    ->modalSubheading('¿Estás seguro de Cerrar la caja? No se puede deshacer, las transacciones de esta caja no podrán editarse.')
                    ->modalButton('Sí, Cerrar Caja')
                    ->color('danger') // Cambiar el color del botón a rojo para resaltar la acción

                    ->icon('heroicon-o-lock-closed'), // Ícono de cerrado
                Tables\Actions\Action::make('pdf')
                    ->label('Imprimir')
                    ->color('success')
                    ->icon('heroicon-s-printer')
                    ->action(function (Cajas $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('pdf', ['record' => $record])
                            )->stream();
                        }, $record->numero_caja . '.pdf');
                    }),
                Tables\Actions\Action::make('procesarTransacciones')
                    ->label('Procesar Stock')
                    ->icon('heroicon-o-check')
                    ->modalHeading('Desea actualizar el stock de productos?')
    ->modalSubheading('Estas seguro de que quieres actualizar el stock? No se puede deshacer.')
    ->modalButton('Sí, Cerrar Caja')
    ->visible(fn($record) => $record->estado === 'cerrado' && $record->fue_procesado == 0)

                    ->action(function (Cajas $record) {
                        // Obtener transacciones relacionadas a esta caja que estén "cerrados"
                        $transacciones = $record->transacciones()
                            ->where('estado_transaccion', 'cerrado')
                            ->where('caja_id', $record->id) // Filtrar por caja_id
                            ->get();

                        foreach ($transacciones as $transaccion) {
                            // Actualizar el estado de la transacción a "Procesado"
                            $transaccion->update(['estado_transaccion' => 'Procesado']);

                            // Procesar el stock de cada producto en los detalles de la transacción
                            foreach ($transaccion->productos as $detalle) {
                                // Obtener el producto relacionado
                                $producto = $detalle->producto;

                                // Restar la cantidad vendida del stock
                                $producto->decrement('stock', $detalle->cantidad);
                            }
                        }
                         // Actualizar 'fue_procesado' a 1 en la caja
        $record->update(['fue_procesado' => 1]);
                    }),


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
            'index' => Pages\ListCajas::route('/'),
            'create' => Pages\CreateCajas::route('/create'),
            'view' => Pages\ViewCajas::route('/{record}'),
            'edit' => Pages\EditCajas::route('/{record}/edit'),
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
