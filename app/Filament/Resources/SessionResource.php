<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SessionResource\Pages;
use App\Filament\Resources\SessionResource\RelationManagers;
use App\Models\Session;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SessionResource extends Resource
{
    protected static ?string $model = Session::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Sesiones';
    protected static ?string $navigationGroup = 'Administración de Usuarios';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('last_activity', '>', now()->subHours(5)->timestamp);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->default('Invitado'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP'),

                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Última actividad')
                    ->since(),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Navegador')
                    ->limit(40),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Solo activas')
                    ->query(fn ($query) => $query->where('last_activity', '>=', now()->subMinutes(config('session.lifetime')))),
            ])
            ->actions([
            ])
            ->defaultSort('last_activity', 'desc');
    }


    public static function canCreate(): bool { return false; }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSessions::route('/'),
        ];
    }
}
