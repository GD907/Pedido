<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiasTrabajadosResource\Pages;
use App\Filament\Resources\DiasTrabajadosResource\RelationManagers;
use App\Models\DiasTrabajados;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiasTrabajadosResource extends Resource
{
    protected static ?string $model = DiasTrabajados::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Administración';
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make([
                Grid::make(2)->schema([
                    Select::make('user_id')
                        ->label('Empleado')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required(),

                    DatePicker::make('fecha')
                        ->label('Fecha')
                        ->default(now())
                        ->required(),

                    Select::make('turno')
                        ->label('Turno')
                        ->options([
                            'mañana' => 'Mañana',
                            'tarde' => 'Tarde',
                            'completo' => 'Completo',
                        ])
                        ->required(),
                ]),

                Textarea::make('observacion')
                    ->label('Observación')
                    ->rows(3)
                    ->maxLength(500),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('fecha', 'desc')
        ->columns([
            TextColumn::make('user.name')
                ->label('Empleado')
                ->searchable()
                ->sortable(),

                TextColumn::make('fecha')
                ->label('Fecha')
                ->sortable()
                ->formatStateUsing(fn ($state) => ucfirst(\Carbon\Carbon::parse($state)->translatedFormat('l d/m/Y'))),

                BadgeColumn::make('turno')
                ->label('Turno')
                ->colors([
                    'success' => 'completo',
                    'warning' => fn ($state) => in_array($state, ['mañana', 'tarde']),
                ])
                ->sortable()
                ->formatStateUsing(fn ($state) => ucfirst($state)),
            TextColumn::make('observacion')
                ->label('Observación')
                ->limit(40)
                ->wrap(),
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make(),

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
            'index' => Pages\ListDiasTrabajados::route('/'),
            'create' => Pages\CreateDiasTrabajados::route('/create'),
            'view' => Pages\ViewDiasTrabajados::route('/{record}'),
            'edit' => Pages\EditDiasTrabajados::route('/{record}/edit'),
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
