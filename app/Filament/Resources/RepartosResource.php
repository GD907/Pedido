<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepartosResource\Pages;
use App\Filament\Resources\RepartosResource\RelationManagers;
use App\Models\Repartos;
use Filament\Forms;
use Filament\Forms\Components\{Card, DateTimePicker, Hidden, Select};
use App\Models\User;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
class RepartosResource extends Resource
{
    protected static ?string $model = Repartos::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Arqueos';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([

                    DateTimePicker::make('fecha')
                        ->label('Fecha del Reparto')
                        ->default(now())
                        ->required(),
                        Forms\Components\TextInput::make('zona')
                        ->label('Zona de Reparto')
                        ->maxLength(255)
                        ->placeholder('Ej. Calle 2, Centro de San Cosme, etc.'),
                    Hidden::make('creado_por')
                        ->default(fn () => auth()->id()),

                        Select::make('repartidor')
                        ->label('Repartidor')
                        ->options(\App\Models\User::pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Hidden::make('estado_reparto')
                        ->default('pendiente'),
                        Forms\Components\TextInput::make('cantidad_pedidos')
                        ->label('Cantidad de Pedidos')
                        ->disabled()
                        ->default(0)
                        ->numeric(),
                        TextInput::make('total_efectivo')
                        ->label('Total en Efectivo')
                        ->disabled()
                        ->numeric()
                        ->default(0),

                    TextInput::make('total_transferencia')
                        ->label('Total en Transferencia')
                        ->disabled()
                        ->numeric()
                        ->default(0),

                    TextInput::make('total_tarjeta')
                        ->label('Total en Tarjeta')
                        ->disabled()
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('monto_total')
                        ->label('Monto Total')
                        ->disabled()
                        ->default(0)
                        ->numeric()
                    // Hidden::make('procesado')
                    //     ->default(false),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha', 'desc')
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->sortable()
                    ->formatStateUsing(fn ($state) =>
                        ucfirst(\Carbon\Carbon::parse($state)->translatedFormat('l d/m/Y - H:i'))
                    ),

                BadgeColumn::make('estado_reparto')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'entregado',
                        'danger' => 'cancelado',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('usuarioRepartidor.name')
                    ->label('Repartidor')
                    ->sortable(),

                TextColumn::make('zona')
                    ->label('Zona')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('monto_total')
                    ->label('Monto Total')
                    ->money('PYG', true)
                    ->sortable(),

                TextColumn::make('creador.name')
                    ->label('Creado por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->estado_reparto === 'pendiente'),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->estado_reparto === 'pendiente'),

                Tables\Actions\Action::make('cerrarReparto')
                    ->label('Cerrar Reparto')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->estado_reparto === 'pendiente')
                    ->requiresConfirmation()
                    ->modalHeading('¿Cerrar Reparto?')
                    ->modalSubheading('Esto actualizará los totales y marcará el reparto como cerrado.')
                    ->modalButton('Sí, cerrar')
                    ->action(function (\App\Models\Repartos $record) {
                        $pedidos = \App\Models\Pedido::where('reparto_id', $record->id)
                            ->where('estado_pedido', 'pendiente')
                            ->get();

                        $totales = [
                            'efectivo' => 0,
                            'transferencia' => 0,
                            'tarjeta' => 0,
                        ];

                        foreach ($pedidos as $pedido) {
                            $metodo = $pedido->metodo_pago;
                            if (in_array($metodo, ['efectivo', 'transferencia', 'tarjeta'])) {
                                $totales[$metodo] += $pedido->total_venta;
                            }

                            $pedido->update(['estado_pedido' => 'cerrado']);
                        }

                        $record->update([
                            'estado_reparto' => 'entregado',
                            'cantidad_pedidos' => $pedidos->count(),
                            'monto_total' => array_sum($totales),
                            'total_efectivo' => $totales['efectivo'],
                            'total_transferencia' => $totales['transferencia'],
                            'total_tarjeta' => $totales['tarjeta'],
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
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
            'index' => Pages\ListRepartos::route('/'),
            'create' => Pages\CreateRepartos::route('/create'),
            'view' => Pages\ViewRepartos::route('/{record}'),
            'edit' => Pages\EditRepartos::route('/{record}/edit'),
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
