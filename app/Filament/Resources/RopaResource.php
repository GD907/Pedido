<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RopaResource\Pages;
use App\Filament\Resources\RopaResource\RelationManagers;
use App\Models\Ropa;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\ToggleColumn;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
class RopaResource extends Resource
{
    protected static ?string $model = Ropa::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Registrar Venta de Ropa')
    ->description('Descripción: Remeras,Juguetes,Etc.    Precio: Ingresar Monto total a Cobrar')
    ->schema([
        Forms\Components\DateTimePicker::make('fecha')
        ->default(fn() => now())->disabled(),
        Forms\Components\TextInput::make('descripcion')
        ->maxLength(255),
        Forms\Components\TextInput::make('precio')
        ->required()
        ->numeric(),


        // ...
    ])

                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('fecha', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                ->sortable()
                ->label('Fecha'),
                Tables\Columns\TextColumn::make('descripcion')->sortable()->searchable()  ->label('Descripción'),
                Tables\Columns\TextColumn::make('precio')->label('Total')
                ->formatStateUsing(function ($state) {
                    // Divide por 100 si el valor original incluye centavos y luego formatea sin decimales
                    $formattedValue = number_format($state, 0, '', '.');
                    return $formattedValue . ' Gs';

                }),
                ToggleColumn::make('cobrado')
                ->label('Cobrado') // Etiqueta para la columna

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
                FilamentExportBulkAction::make('Imprimir')
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
