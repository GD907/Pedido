<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Filament\Resources\PedidoResource\RelationManagers;
use App\Models\Pedido;
use Filament\Forms;
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

use Filament\Forms\Components\Repeater;
class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Card::make()
                ->schema([
                    Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->user()->id),
                    Forms\Components\Select::make('cliente_id')
                    ->label('Cliente')
                        ->required()->options(Cliente::all()
                        ->pluck('nombre', 'id'))
                        ->preload()
                        ->searchable(),
                        Forms\Components\TextInput::make('numero_factura')
                        ->disabled()
                        ->default('OR-' . random_int(100000, 999999)),
                        Forms\Components\TextInput::make('observacion')
                            ->maxLength(255),
                    Forms\Components\DateTimePicker::make('fecha')
                        ->default(fn () => now())->disabled(),
                        Forms\Components\Select::make('estado_pedidos_id')
                        ->relationship('estado_pedidos', 'nombre')->default('pendiente')->default(1)
                        ->label('Estado'),
                    Forms\Components\TextInput::make('total_venta')//Quiero guaradar en este la suma de todas las ventas
                        ->reactive(),
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
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('precio', Producto::find($state)?->precio ?? 0))

                                    ->columnSpan([
                                        'md' => 5,
                                    ]),

                                Forms\Components\TextInput::make('cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => $set('subtotal', $state * $get('precio')))
                                    ->columnSpan([
                                        'md' => 2,
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio Unitario')
                                    ->disabled()
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('subtotal', Producto::find($state)?->precio ?? 0))
                                    ->columnSpan([
                                        'md' => 3,
                                    ]),
                                    Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->disabled()
                                    ->reactive()
                                    ->numeric()
                                    ->required()
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
                //
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
