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
                            ->default(fn () => now())->disabled(),
                            Forms\Components\TextInput::make('observacion')
                            ->maxLength(255)
                            ->columnSpan([
                                'md' => 3,
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
                                                'md' => 3,
                                            ]),

                                        Forms\Components\TextInput::make('precioventa')
                                            ->label('Precio Venta')

                                            ->numeric()
                                            ->reactive()

                                            ->postfix('Gs')
                                            ->columnSpan([
                                                'md' => 3,
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
            ->columns([
                Tables\Columns\TextColumn::make('fecha')->label('Fecha de Entrada')->searchable(),

            ])
            ->filters([
                //
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
