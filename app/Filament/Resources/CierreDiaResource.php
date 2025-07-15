<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CierreDiaResource\Pages;
use App\Filament\Resources\CierreDiaResource\RelationManagers;
use App\Models\CierreDia;
use Filament\Forms\Components\{Card, DateTimePicker, TextInput, Select, Hidden, Placeholder};
use App\Models\Cajas;
use App\Models\Repartos;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\CierreDelDia;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;

class CierreDiaResource extends Resource
{
    protected static ?string $model = CierreDia::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Arqueos';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // CARD PRINCIPAL
                Card::make()->schema([
                    DateTimePicker::make('fecha_hora_cierre')
                        ->label('Fecha y Hora del Cierre')
                        ->default(now())
                        ->required()
                        ->disabled()
                        ->dehydrated(true),

                    Hidden::make('creado_por')
                        ->default(fn() => Auth::id()),

                    TextInput::make('dinero_inicial')
                        ->label('Dinero Inicial en Caja')
                        ->numeric()
                        ->default(0),
                ])->columns(2),

                // CARD CAJA
                Card::make()->schema([
                    Placeholder::make('caja_titulo')
                        ->content('🟢 Caja del Día')
                        ->columnSpanFull(),

                    // Select en una fila sola
                    Select::make('caja_id')
                        ->label('Caja Relacionada')
                        ->options(\App\Models\Cajas::pluck('numero_caja', 'id'))
                        ->searchable()
                        ->nullable()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $caja = \App\Models\Cajas::find($state);
                            $set('caja_efectivo', $caja?->total_trx_efectivo + $caja?->total_ropa_efectivo + $caja?->total_boleta_efectivo);
                            $set('caja_transferencia', $caja?->total_trx_transferencia + $caja?->total_ropa_transferencia + $caja?->total_boleta_transferencia);
                            $set('caja_tarjeta', $caja?->total_trx_tarjeta + $caja?->total_ropa_tarjeta + $caja?->total_boleta_tarjeta);
                            $set('caja_total', $caja?->total_caja ?? 0);
                        })
                        ->columnSpan(3),

                    // Caja Efectivo - Transferencia - Tarjeta en una fila
                    TextInput::make('caja_efectivo')
                        ->label('Caja - Efectivo')
                        ->disabled()
                        ->default(0)
                        ->columnSpan(1),

                    TextInput::make('caja_transferencia')
                        ->label('Caja - Transferencia')
                        ->disabled()
                        ->default(0)
                        ->columnSpan(1),

                    TextInput::make('caja_tarjeta')
                        ->label('Caja - Tarjeta')
                        ->disabled()
                        ->default(0)
                        ->columnSpan(1),

                    // Caja Total en su propia fila
                    TextInput::make('caja_total')
                        ->label('Caja - Total')
                        ->disabled()
                        ->default(0)
                        ->columnSpan(3),
                ])->columns(3),

                Card::make()->schema([
                    Placeholder::make('reparto_titulo')
                        ->content('📦 Reparto del Día')
                        ->columnSpanFull(),

                    // Select en una sola fila
                    Select::make('reparto_id')
                        ->label('Reparto Relacionado')
                        ->options(\App\Models\Repartos::pluck('zona', 'id'))
                        ->searchable()
                        ->nullable()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $reparto = \App\Models\Repartos::find($state);
                            $set('reparto_efectivo', $reparto?->total_efectivo ?? 0);
                            $set('reparto_transferencia', $reparto?->total_transferencia ?? 0);
                            $set('reparto_tarjeta', $reparto?->total_tarjeta ?? 0);
                            $set('reparto_total', $reparto?->monto_total ?? 0);
                        })
                        ->columnSpanFull(),

                    // Totales en una sola fila de 3 columnas
                    TextInput::make('reparto_efectivo')
                        ->label('Reparto - Efectivo')
                        ->disabled()
                        ->default(0)
                        ->columnSpan(1),
                    TextInput::make('reparto_transferencia')
                        ->label('Reparto - Transferencia')
                        ->disabled()
                        ->default(0)
                        ->columnSpan(1),
                    TextInput::make('reparto_tarjeta')
                        ->label('Reparto - Tarjeta')
                        ->disabled()
                        ->default(0)
                        ->columnSpan(1),

                    // Total en fila completa
                    TextInput::make('reparto_total')
                        ->label('Reparto - Total')
                        ->disabled()
                        ->default(0)
                        ->columnSpanFull(),
                ])->columns(3),

                // CARD WEPA
                Card::make()->schema([
                    Placeholder::make('wepa_titulo')->content('🔵 Servicio Wepa')->columnSpanFull(),

                    TextInput::make('wepa_lote')->label('Lote Wepa')->nullable()->columnSpanFull(),

                    TextInput::make('wepa_ingresos')->label('Ingresos')->numeric()->default(0),
                    TextInput::make('wepa_egresos')->label('Egresos')->numeric()->default(0),

                    TextInput::make('wepa_cantidad')->label('Cantidad de Retiros Western')->numeric()->default(0),
                    TextInput::make('wepa_comision')->label('Comisión Total')->numeric()->default(0),
                ])->columns(2),

                // CARD AQUIPAGO
                Card::make()->schema([
                    Placeholder::make('aquipago_titulo')->content('🟣 Servicio Aquipago')->columnSpanFull(),

                    TextInput::make('aquipago_lote')->label('Lote Aquipago')->nullable()->columnSpanFull(),

                    TextInput::make('aquipago_ingresos')->label('Ingresos')->numeric()->default(0),
                    TextInput::make('aquipago_egresos')->label('Egresos')->numeric()->default(0),

                    TextInput::make('aquipago_cantidad')->label('Cantidad de Extracciones')->numeric()->default(0),
                    TextInput::make('aquipago_comision')->label('Comisión Total')->numeric()->default(0),
                ])->columns(2),

                // CARD TOTALES FINALES
                Card::make()->schema([
                    Placeholder::make('totales_titulo')->content('💰 Totales Finales')->columnSpanFull(),

                    TextInput::make('total_efectivo')->label('Total Efectivo')->numeric()->default(0)->disabled(),
                    TextInput::make('total_transferencia')->label('Total Transferencia')->numeric()->default(0)->disabled(),
                    TextInput::make('total_tarjeta')->label('Total Tarjeta')->numeric()->default(0)->disabled(),

                    TextInput::make('total_general')->label('Total General')->numeric()->default(0)->disabled(),
                ])->columns(3),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('fecha_hora_cierre')
                    ->label('Fecha de Cierre')
                    ->sortable()
                    ->formatStateUsing(
                        fn($state) =>
                        ucfirst(\Carbon\Carbon::parse($state)->translatedFormat('l d/m/Y H:i'))
                    ),

                \Filament\Tables\Columns\TextColumn::make('dinero_inicial')
                    ->label('Dinero Inicial')
                    ->formatStateUsing(
                        fn($state) =>
                        number_format($state, 0, '', '.') . ' Gs'
                    ),

                \Filament\Tables\Columns\TextColumn::make('creador.name')
                    ->label('Creado por'),

                \Filament\Tables\Columns\TextColumn::make('total_general')
                    ->label('Total Final en Caja')
                    ->formatStateUsing(
                        fn($state) =>
                        number_format($state, 0, '', '.') . ' Gs'
                    ),
            ])
            ->defaultSort('fecha_hora_cierre', 'desc')
            ->filters([
                \Filament\Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('calcularTotales')
                    ->label('Calcular Totales')
                    ->visible(fn($record) => $record->total_general === null || $record->total_general == 0)
                    ->action(function (\App\Models\CierreDia $record) {
                        $totalEfectivo =
                            $record->dinero_inicial +
                            $record->caja_efectivo +
                            $record->reparto_efectivo +
                            $record->wepa_ingresos +
                            $record->aquipago_ingresos +
                            $record->aquipago_comision +
                            $record->wepa_comision -
                            $record->aquipago_egresos -
                            $record->wepa_egresos;

                        $totalTransferencia =
                            $record->caja_transferencia +
                            $record->reparto_transferencia;

                        $totalTarjeta =
                            $record->caja_tarjeta +
                            $record->reparto_tarjeta;

                        $totalGeneral = $totalEfectivo + $totalTransferencia + $totalTarjeta;

                        $record->update([
                            'total_efectivo' => $totalEfectivo,
                            'total_transferencia' => $totalTransferencia,
                            'total_tarjeta' => $totalTarjeta,
                            'total_general' => $totalGeneral,
                        ]);
                    })
                    ->color('success')
                    ->icon('heroicon-o-calculator')
                    ->requiresConfirmation()
                    ->modalHeading('¿Calcular totales finales?')
                    ->modalSubheading('Esto actualizará los montos totales basados en los datos ingresados.')
                    ->modalButton('Sí, calcular'),
                Tables\Actions\Action::make('pdf')
                    ->label('Imprimir')
                    ->color('success')
                    ->icon('heroicon-s-printer')
                    ->action(function (CierreDia $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('cierre', ['record' => $record])
                            )
                                ->setPaper('a4', 'portrait') // Usar hoja A4
                                ->stream();
                        }, 'Cierre-' . $record->id . '.pdf');
                    }),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
                \Filament\Tables\Actions\ForceDeleteBulkAction::make(),
                \Filament\Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListCierreDias::route('/'),
            'create' => Pages\CreateCierreDia::route('/create'),
            'view' => Pages\ViewCierreDia::route('/{record}'),
            'edit' => Pages\EditCierreDia::route('/{record}/edit'),
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
