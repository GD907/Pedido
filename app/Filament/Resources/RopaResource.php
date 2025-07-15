<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RopaResource\Pages;
use App\Filament\Resources\RopaResource\RelationManagers;
use App\Models\Ropa;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Forms\Components\{Card, DatePicker, TextInput, Select, Hidden};
use App\Models\Cajas;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Tables\Columns\ToggleColumn;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
class RopaResource extends Resource
{
    protected static ?string $model = Ropa::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Ventas';
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make()
                ->schema([
                    // Primera fila: 3 columnas
                    Forms\Components\DateTimePicker::make('fecha')
                        ->label('Fecha de venta')
                        ->required()
                        ->disabled()
                        ->default(now())
                        ->columnSpan(1),

                    Select::make('metodo_pago')
                        ->label('Método de Pago')
                        ->options([
                            'efectivo' => 'Efectivo',
                            'tarjeta' => 'Tarjeta',
                            'transferencia' => 'Transferencia',
                        ])
                        ->required()
                        ->default('efectivo')
                        ->columnSpan(1),

                        Select::make('caja_id')
                        ->label('Caja')
                        ->placeholder('Seleccione una caja')
                        ->options(fn () => \App\Models\Cajas::where('estado', 'abierto')
                            ->pluck('numero_caja', 'id'))
                        ->default(fn () => \App\Models\Cajas::where('estado', 'abierto')
                            ->pluck('id')
                            ->first())
                        ->preload()
                        ->searchable()
                        ->nullable()
                        ->helperText('Solo se muestran cajas abiertas.')
                        ->columnSpan(1),

                    // Segunda fila: descripción ocupa las 3 columnas
                    TextInput::make('descripcion')
                        ->label('Descripción')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    // Tercera fila: 2 columnas
                    TextInput::make('unidades')
                        ->label('Cantidad de unidades')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->columnSpan(1),

                    TextInput::make('precio')
                        ->label('Total (Gs)')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->columnSpan(1),

                    // Hidden user_id
                    Hidden::make('creado_por')
                        ->default(fn () => Auth::id()),
                ])
                ->columns(1)
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
            BadgeColumn::make('estado')
            ->label('Estado')
            ->colors([
                'warning' => 'pendiente',
                'danger'  => 'cancelado',
                'success' => 'cerrado',
            ])
            ->formatStateUsing(fn($state) => ucfirst($state)),
            TextColumn::make('descripcion')
                ->label('Descripción')
                ->sortable()
                ->searchable(),

            TextColumn::make('precio')
                ->label('Total')
                ->formatStateUsing(fn ($state) =>
                    number_format($state, 0, '', '.') . ' Gs'
                ),

            TextColumn::make('metodo_pago')
                ->label('Método de Pago')
                ->formatStateUsing(fn ($state) => ucfirst($state)),

            TextColumn::make('caja.numero_caja')
                ->label('Caja')


                ->formatStateUsing(function ($state) {
                    return match ($state) {
                        'pendiente' => 'En curso',
                        'cancelado' => 'Cancelado',
                        'cerrado'   => 'Cerrado',
                        default     => ucfirst($state),
                    };
                })
                ->sortable(),
            TextColumn::make('creador.name')
                ->label('Creado por')
                ->sortable(),
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make(),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make()
            ->visible(fn ($record) => $record->estado !== 'cerrado'),

        Tables\Actions\DeleteAction::make()
            ->visible(fn ($record) => $record->estado !== 'cerrado'),
            Tables\Actions\Action::make('cancelar')
    ->label('Cancelar')
    ->icon('heroicon-o-x-circle')
    ->color('danger')
    ->requiresConfirmation()
    ->modalHeading('¿Cancelar venta?')
    ->modalSubheading('¿Estás seguro de cancelar esta venta de ropa? Esta acción no se puede deshacer.')
    ->modalButton('Sí, cancelar')
    ->action(fn ($record) => $record->update(['estado' => 'cancelado']))
    ->visible(fn ($record) =>
    !in_array($record->estado, ['cancelado', 'cerrado']) &&
    auth()->user()->hasRole('super_admin')
)
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
            Tables\Actions\ForceDeleteBulkAction::make(),
            Tables\Actions\RestoreBulkAction::make(),
            \AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction::make('Imprimir')
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
            'index' => Pages\ListRopas::route('/'),
            'create' => Pages\CreateRopa::route('/create'),
            'view' => Pages\ViewRopa::route('/{record}'),
            'edit' => Pages\EditRopa::route('/{record}/edit'),
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
