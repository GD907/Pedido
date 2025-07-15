<?php

namespace App\Filament\Resources;

use App\Models\Transacciones;
use App\Filament\Resources\CajasResource\Pages;
use App\Filament\Resources\CajasResource\RelationManagers;
use App\Models\Cajas;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Carbon;
class CajasResource extends Resource
{
    protected static ?string $model = Cajas::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Arqueos';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    // Fila 1: Fecha, Número de Caja, Encargado (nombre)
                    TextInput::make('fecha')
                        ->label('Fecha de Apertura')
                        ->disabled()
                        ->columnSpan(1),

                    TextInput::make('numero_caja')
                        ->label('Número de Caja')
                        ->disabled()
                        ->columnSpan(1),

                    TextInput::make('users.name')
                        ->label('Encargado')
                        ->disabled()
                        ->columnSpan(1),

                    // Fila 2: Observación (columna completa)
                    TextInput::make('observacion')
                        ->label('Observación')
                        ->columnSpanFull(),

                    // Subtítulo: Transacciones
                    Placeholder::make('')
                        ->content('🧾 Transacciones')
                        ->extraAttributes(['class' => 'text-lg font-semibold text-gray-700'])
                        ->columnSpanFull(),

                    TextInput::make('total_trx_efectivo')->label('Efectivo')->default('0')->disabled(),
                    TextInput::make('total_trx_transferencia')->label('Transferencia')->default('0')->disabled(),
                    TextInput::make('total_trx_tarjeta')->label('Tarjeta')->default('0')->disabled(),

                    TextInput::make('cantidad_trx')->label('Cantidad')->default('0')->disabled(),
                    TextInput::make('contador_cancelados')->label('Cancelados')->default('0')->disabled(),
                    TextInput::make('total_trx_general')->label('Total General')->default('0')->disabled(),

                    // Subtítulo: Ropas
                    Placeholder::make('')
                        ->content('👕 Ropas')
                        ->extraAttributes(['class' => 'text-lg font-semibold text-gray-700'])
                        ->columnSpanFull(),

                    TextInput::make('total_ropa_efectivo')->label('Efectivo')->default('0')->disabled(),
                    TextInput::make('total_ropa_transferencia')->label('Transferencia')->default('0')->disabled(),
                    TextInput::make('total_ropa_tarjeta')->label('Tarjeta')->default('0')->disabled(),

                    TextInput::make('cantidad_ventas_ropa')->label('Cantidad')->default('0')->disabled(),
                    TextInput::make('contador_ropas_canceladas')->label('Cancelados')->default('0')->disabled(),
                    TextInput::make('total_ropa_general')->label('Total General')->default('0')->disabled(),

                    // Subtítulo: Boletas
                    Placeholder::make('')
                        ->content('📦 Boletas')
                        ->extraAttributes(['class' => 'text-lg font-semibold text-gray-700'])
                        ->columnSpanFull(),

                    TextInput::make('total_boleta_efectivo')->label('Efectivo')->default('0')->disabled(),
                    TextInput::make('total_boleta_transferencia')->label('Transferencia')->default('0')->disabled(),
                    TextInput::make('total_boleta_tarjeta')->label('Tarjeta')->default('0')->disabled(),

                    TextInput::make('cantidad_boletas')->label('Cantidad')->default('0')->disabled(),
                    TextInput::make('contador_pedidos_cancelados')->label('Cancelados')->default('0')->disabled(),
                    TextInput::make('total_boleta_general')->label('Total General')->default('0')->disabled(),

                    // Subtítulo: Totales generales
                    Placeholder::make('')
                        ->content('💰 Totales Generales')
                        ->extraAttributes(['class' => 'text-lg font-semibold text-indigo-700'])
                        ->columnSpanFull(),

                    TextInput::make('total_efectivo_general')->label('Efectivo General')->default('0')->disabled(),
                    TextInput::make('total_transferencia_general')->label('Transferencia General')->default('0')->disabled(),
                    TextInput::make('total_tarjeta_general')->label('Tarjeta General')->default('0')->disabled(),

                    // Hora de cierre en fila sola
                    TextInput::make('cierre')->label('Hora de Cierre')->disabled()->columnSpanFull(),

                    // Total caja al final
                    TextInput::make('total_caja')->label('Total Caja')->default('0')->disabled()->columnSpanFull(),

                    // Hidden para creación
                    Hidden::make('fecha')->default(now()->format('Y-m-d H:i:s')),
                    Hidden::make('numero_caja')->default(function () {
                        $ultimaCCN = \App\Models\Cajas::withTrashed()
                            ->where('numero_caja', 'like', 'CCN-%')
                            ->orderByDesc('id')
                            ->first()?->numero_caja;

                        $siguiente = ($ultimaCCN && preg_match('/CCN-(\d+)/', $ultimaCCN, $matches)) ? (int)$matches[1] + 1 : 1;

                        return 'CCN-' . $siguiente;
                    }),
                    Hidden::make('users_id')->default(fn () => auth()->id()),

                ])->columns(3),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('fecha', 'desc')
        ->columns([
            Tables\Columns\TextColumn::make('numero_caja')
                ->label('N°')
                ->searchable(),

            Tables\Columns\TextColumn::make('fecha')
                ->label('Apertura')
                ->sortable()
                ->formatStateUsing(fn ($state) =>
                    ucfirst(\Carbon\Carbon::parse($state)->translatedFormat('l d/m/Y - H:i'))
                ),

            Tables\Columns\TextColumn::make('cierre')
                ->label('Cierre')
                ->sortable()
                ->formatStateUsing(fn ($state) =>
                    $state ? ucfirst(\Carbon\Carbon::parse($state)->translatedFormat('l d/m/Y - H:i')) : '-'
                ),

            Tables\Columns\TextColumn::make('users.name')
                ->label('Encargado')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('estado')
                ->label('Estado')
                ->sortable()
                ->searchable()
                ->getStateUsing(fn ($record) => $record->estado === 'abierto' ? 'Abierto' : 'Cerrado')
                ->color(fn ($record) => $record->estado === 'abierto' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('total_caja')
                ->label('Total Recaudado')
                ->sortable()
                ->formatStateUsing(fn ($state, $record) =>
                    $record?->estado === 'cerrado'
                        ? number_format($state ?? 0, 0, '', '.') . ' Gs'
                        : '-'
            ),


            // Tables\Columns\TextColumn::make('fue_procesado')
            //     ->label('Procesado')
            //     ->sortable()
            //     ->formatStateUsing(fn ($state) => $state === 1 ? 'Sí' : 'No')
            //     ->color(fn ($record) => $record->fue_procesado === 1 ? 'success' : 'danger'),
        ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cerrarCaja')
                ->label('Cerrar Caja')
                ->visible(fn($record) => $record->estado === 'abierto')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Cierre de Caja')
                ->modalSubheading('¿Estás seguro de cerrar la caja? Esta acción no se puede deshacer.')
                ->modalButton('Sí, Cerrar Caja')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->action(function (Cajas $record) {
                    $cajaId = $record->id;

                    // --- Transacciones ---
                    $transacciones = \App\Models\Transacciones::where('caja_id', $cajaId)
                        ->where('estado_transaccion', 'en curso')
                        ->get();

                    $totalesTrx = [
                        'efectivo' => 0,
                        'tarjeta' => 0,
                        'transferencia' => 0,
                    ];

                    foreach ($transacciones as $trx) {
                        $totalesTrx[$trx->metodo_pago] += $trx->total_trx;
                        $trx->update(['estado_transaccion' => 'cerrado']);
                    }

                    // --- Ventas de Ropa ---
                    $ventasRopa = \App\Models\Ropa::where('caja_id', $cajaId)
                        ->where('estado', 'pendiente')
                        ->get();

                    $ropasCanceladas = \App\Models\Ropa::where('caja_id', $cajaId)
                        ->where('estado', 'cancelado')
                        ->count();

                    $totalesRopa = [
                        'efectivo' => 0,
                        'tarjeta' => 0,
                        'transferencia' => 0,
                    ];

                    foreach ($ventasRopa as $ropa) {
                        $totalesRopa[$ropa->metodo_pago] += $ropa->precio;
                        $ropa->update(['estado' => 'cerrado']);
                    }

                    // --- Pedidos ---
                    $pedidos = \App\Models\Pedido::where('caja_id', $cajaId)
                        ->where('cobrado_por', 'caja')
                        ->where('estado_pedido', 'pendiente')
                        ->get();

                    $pedidosCancelados = \App\Models\Pedido::where('caja_id', $cajaId)
                        ->where('cobrado_por', 'caja')
                        ->where('estado_pedido', 'cancelado')
                        ->count();

                    $totalesBoletas = [
                        'efectivo' => 0,
                        'tarjeta' => 0,
                        'transferencia' => 0,
                    ];

                    foreach ($pedidos as $pedido) {
                        $totalesBoletas[$pedido->metodo_pago] += $pedido->total_venta;
                        $pedido->update(['estado_pedido' => 'cerrado']);
                    }

                    // --- Guardar ---
                    $record->update([
                        'estado' => 'cerrado',
                        'cierre' => now(),

                        // Totales transacciones
                        'total_trx_efectivo' => $totalesTrx['efectivo'],
                        'total_trx_tarjeta' => $totalesTrx['tarjeta'],
                        'total_trx_transferencia' => $totalesTrx['transferencia'],
                        'total_trx_general' => array_sum($totalesTrx),

                        // Totales ropas
                        'total_ropa_efectivo' => $totalesRopa['efectivo'],
                        'total_ropa_tarjeta' => $totalesRopa['tarjeta'],
                        'total_ropa_transferencia' => $totalesRopa['transferencia'],
                        'total_ropa_general' => array_sum($totalesRopa),

                        // Totales pedidos
                        'total_boleta_efectivo' => $totalesBoletas['efectivo'],
                        'total_boleta_tarjeta' => $totalesBoletas['tarjeta'],
                        'total_boleta_transferencia' => $totalesBoletas['transferencia'],
                        'total_boleta_general' => array_sum($totalesBoletas),

                        // Cantidades
                        'cantidad_trx' => $transacciones->count(),
                        'cantidad_ventas_ropa' => $ventasRopa->count(),
                        'cantidad_boletas' => $pedidos->count(),

                        // Cancelados
                        'contador_ropas_canceladas' => $ropasCanceladas,
                        'contador_pedidos_cancelados' => $pedidosCancelados,

                        // Total general
                        'total_caja' => array_sum($totalesTrx) + array_sum($totalesRopa) + array_sum($totalesBoletas),

                        // Totales combinados por método de pago
                        'total_efectivo_general' => $totalesTrx['efectivo'] + $totalesRopa['efectivo'] + $totalesBoletas['efectivo'],
                        'total_tarjeta_general' => $totalesTrx['tarjeta'] + $totalesRopa['tarjeta'] + $totalesBoletas['tarjeta'],
                        'total_transferencia_general' => $totalesTrx['transferencia'] + $totalesRopa['transferencia'] + $totalesBoletas['transferencia'],
                    ]);
                }),
                Tables\Actions\Action::make('pdf')
                    ->label('Imprimir')
                    ->color('success')
                    ->icon('heroicon-s-printer')
                    ->action(function (Cajas $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('pdf', ['record' => $record])
                            )->setPaper([0, 0, 198.45, 200], 'portrait')  // Ancho 80 mm en puntos, ajusta la altura según el contenido
                            ->stream();
                        }, $record->numero_caja . '.pdf');
                    }),

    //             Tables\Actions\Action::make('procesarTransacciones')
    //                 ->label('Procesar Stock')
    //                 ->icon('heroicon-o-check')
    //                 ->modalHeading('Desea actualizar el stock de productos?')
    // ->modalSubheading('Estas seguro de que quieres actualizar el stock? No se puede deshacer.')
    // ->modalButton('Sí, Actualizar s')
    // ->visible(fn($record) => $record->estado === 'cerrado' && $record->fue_procesado == 0)

    //                 ->action(function (Cajas $record) {
    //                     // Obtener transacciones relacionadas a esta caja que estén "cerrados"
    //                     $transacciones = $record->transacciones()
    //                         ->where('estado_transaccion', 'cerrado')
    //                         ->where('caja_id', $record->id) // Filtrar por caja_id
    //                         ->get();

    //                     foreach ($transacciones as $transaccion) {
    //                         // Actualizar el estado de la transacción a "Procesado"
    //                         $transaccion->update(['estado_transaccion' => 'Procesado']);

    //                         // Procesar el stock de cada producto en los detalles de la transacción
    //                         foreach ($transaccion->productos as $detalle) {
    //                             // Obtener el producto relacionado
    //                             $producto = $detalle->producto;

    //                             // Restar la cantidad vendida del stock
    //                             $producto->decrement('stock', $detalle->cantidad);
    //                         }
                        // }
                         // Actualizar 'fue_procesado' a 1 en la caja
        // $record->update(['fue_procesado' => 1]);
        //             }),


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
            'index' => Pages\ListCajas::route('/'),
            'create' => Pages\CreateCajas::route('/create'),
            'view' => Pages\ViewCajas::route('/{record}'),
            'edit' => Pages\EditCajas::route('/{record}/edit'),
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
