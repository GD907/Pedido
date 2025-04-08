<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarioChequesResource\Pages;
use App\Filament\Resources\CalendarioChequesResource\RelationManagers;
use App\Models\CalendarioCheques;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;

class CalendarioChequesResource extends Resource
{
    protected static ?string $model = CalendarioCheques::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    Grid::make(2)->schema([
                        TextInput::make('numero_cheque')
                            ->label('Número de Cheque')
                            ->required()
                            ->extraAttributes([
                                'autocomplete' => 'off',
                                'autocorrect' => 'off',
                                'autocapitalize' => 'off',
                                'spellcheck' => 'false',
                            ])
                            ->maxLength(50),

                        TextInput::make('monto')
                            ->label('Monto (Gs)')
                            ->numeric()
                            ->required(),

                        TextInput::make('banco')
                            ->required(),

                        TextInput::make('proveedor')
                            ->required(),

                        DatePicker::make('fecha_emitida')
                            ->label('Fecha Emitida')
                            ->required()
                            ->default(now()),

                        DatePicker::make('fecha_vencimiento')
                            ->label('Fecha de Vencimiento')
                            ->required(),

                        TextInput::make('firmado_por')
                            ->label('Firmado por')
                            ->required(),

                        Select::make('estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'cobrado' => 'Cobrado',
                                'rechazado' => 'Rechazado',
                            ])
                            ->default('pendiente')
                            ->required(),
                    ]),

                    Textarea::make('concepto')
                        ->label('Concepto o razón del pago')
                        ->rows(3)
                        ->maxLength(500),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('fecha_vencimiento', 'asc')
            ->columns([

                TextColumn::make('proveedor')
                    ->label('Proveedor')
                    ->searchable(),

                TextColumn::make('fecha_vencimiento')
                ->label('Vence')
                ->date()
                ->sortable(),
                BadgeColumn::make('vencimiento_proximidad')
    ->label('Proximidad')
    ->getStateUsing(function ($record) {
        return now()->diffInDays($record->fecha_vencimiento, false);
    })
    ->colors([
        'success' => fn ($state) => $state > 16,
        'warning' => fn ($state) => $state <= 16 && $state > 8,
        'danger'  => fn ($state) => $state <= 8,
    ])
    ->formatStateUsing(fn ($state) => $state > 0
        ? "Faltan {$state} días"
        : "Venció hace " . abs($state) . " días"),

                TextColumn::make('fecha_emitida')
                ->label('Emitido')
                ->date()
                ->sortable(),

                TextColumn::make('banco')
                    ->label('Banco'),

                TextColumn::make('monto')
                    ->label('Monto (Gs)')
                    ->money('PYG', true),


                    BadgeColumn::make('estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'cobrado',
                        'danger' => 'rechazado',
                    ])
                    ->label('Estado')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(), // Para ver eliminados con soft delete
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListCalendarioCheques::route('/'),
            'create' => Pages\CreateCalendarioCheques::route('/create'),
            'view' => Pages\ViewCalendarioCheques::route('/{record}'),
            'edit' => Pages\EditCalendarioCheques::route('/{record}/edit'),
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
