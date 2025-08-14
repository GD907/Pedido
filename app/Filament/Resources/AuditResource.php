<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditResource\Pages;
use App\Filament\Resources\AuditResource\RelationManagers;
use App\Models\Audit;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-search';
    protected static ?string $navigationLabel = 'Auditorías';
    protected static ?string $navigationGroup = 'Administración de Usuarios';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    Forms\Components\TextInput::make('event')
                    ->label('Evento')
                    ->disabled()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'created' => 'Creado',
                        'updated' => 'Modificado',
                        'deleted' => 'Eliminado',
                        default => ucfirst($state),
                    }),

                    Forms\Components\Placeholder::make('user_name')
                    ->label('Usuario')
                    ->content(fn ($record) => $record->user?->name ?? 'Sistema'),
                    Forms\Components\Placeholder::make('modelo_auditado')
                    ->label('Modelo auditado')
                    ->content(fn ($record) => class_basename($record->auditable_type)),
                    Forms\Components\TextInput::make('auditable_id')->label('ID del modelo')->disabled(),

                    Forms\Components\Textarea::make('old_values')
                        ->label('Valores anteriores')
                        ->disabled()
                        ->rows(4)
                        ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),

                    Forms\Components\Textarea::make('new_values')
                        ->label('Valores nuevos')
                        ->disabled()
                        ->rows(4)
                        ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),

                    Forms\Components\TextInput::make('url')->disabled(),
                    Forms\Components\TextInput::make('ip_address')->label('IP')->disabled(),
                    Forms\Components\Textarea::make('user_agent')->label('Navegador')->disabled(),
                    Forms\Components\TextInput::make('created_at')->label('Fecha')->disabled(),
                ]),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event')
                ->label('Evento')
                ->sortable()
                ->formatStateUsing(fn ($state) => match ($state) {
                    'created' => 'Creado',
                    'updated' => 'Modificado',
                    'deleted' => 'Eliminado',
                    default => ucfirst($state),
                }),
                Tables\Columns\TextColumn::make('user.name')
                ->label('Usuario')
                ->sortable()
                ->default('Desconocido'),
                Tables\Columns\TextColumn::make('auditable_type')
                ->sortable()
                ->searchable()
                ->label('Modelo')
                ->formatStateUsing(fn ($state) => class_basename($state))
                ->searchable(),
                Tables\Columns\TextColumn::make('auditable_id')->sortable()->label('ID'),

                Tables\Columns\TextColumn::make('created_at')->sortable()->label('Fecha')->since(),
            ])
            ->filters([
                  // Filtro por tipo de evento
            Tables\Filters\SelectFilter::make('event')
            ->label('Evento')
            ->options([
                'created' => 'Creado',
                'updated' => 'Modificado',
                'deleted' => 'Eliminado',
            ]),

        // Filtro por modelo auditado
        Tables\Filters\SelectFilter::make('auditable_type')
            ->label('Modelo auditado')
            ->options(
                Audit::query()
                    ->select('auditable_type')
                    ->distinct()
                    ->pluck('auditable_type')
                    ->mapWithKeys(fn ($item) => [$item => class_basename($item)])
            ),

        // Filtro por usuario
        Tables\Filters\SelectFilter::make('user_id')
            ->label('Usuario')
            ->relationship('user', 'name'),

        // Filtro por fecha
        Tables\Filters\Filter::make('created_at')
            ->form([
                Forms\Components\DatePicker::make('from')->label('Desde'),
                Forms\Components\DatePicker::make('until')->label('Hasta'),
            ])
            ->query(function ($query, array $data) {
                return $query
                    ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                    ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
            }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

            ]) ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAudits::route('/'),
        ];
    }
}
