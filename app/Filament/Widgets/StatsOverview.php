<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Transacciones;
use App\Models\Ropa;
use App\Models\CalendarioCheques;
use App\Models\Cajas; // 👈 agregado
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $hoy = Carbon::today();

        // Totales base
        $totalClientes = Cliente::count();

        // 🔹 Pedidos de hoy (excluye cancelados)
        $pedidosQuery = Pedido::query()
            ->whereDate('fecha', $hoy)
            ->where(function ($q) {
                $q->whereNull('estado_pedido')
                  ->orWhere('estado_pedido', '!=', 'cancelado');
            });

        $pedidosHoy      = (int) $pedidosQuery->count();
        $pedidosHoyTotal = (int) (clone $pedidosQuery)->sum('total_venta');

        // 🔹 Transacciones de hoy (EXCLUYENDO canceladas)
        $transQuery = Transacciones::query()
            ->whereDate('fecha', $hoy)
            ->where(function ($q) {
                $q->whereNull('estado_transaccion')
                  ->orWhere('estado_transaccion', '!=', 'cancelado');
            });

        $transHoyCant  = (int) $transQuery->count();
        $transHoyTotal = (int) (clone $transQuery)
            ->sum(DB::raw('COALESCE(total_con_descuento, total_trx, 0)'));

        // 🔹 Ropas del día (todas menos canceladas)
        $ropaQuery = Ropa::query()
            ->whereDate('fecha', $hoy)
            ->where(function ($q) {
                $q->whereNull('estado')
                  ->orWhereNotIn('estado', ['cancelado']);
            });

        $ropaHoyCant  = (int) $ropaQuery->count();
        $ropaHoyTotal = (int) (clone $ropaQuery)->sum('precio');

        // 🔹 Cheques pendientes a cobrar
        $chequesPendCant  = CalendarioCheques::where('estado', 'pendiente')->count();
        $chequesPendTotal = (int) CalendarioCheques::where('estado', 'pendiente')->sum('monto');

        // 🔹 Monto en Cajas Abiertas Hoy
        $abiertasQuery = Cajas::query()
            ->where('estado', 'abierto')
            ->whereDate('fecha', $hoy);

        $cajasAbiertasCant  = (int) $abiertasQuery->count();
        $cajasAbiertasMonto = (int) (clone $abiertasQuery)->sum(DB::raw('COALESCE(total_caja,0)'));

        // Helpers de formato (PYG sin decimales)
        $fmt = fn ($n) => number_format((int) $n, 0, '', '.');
        $pyg = fn ($n) => 'Gs ' . $fmt($n);

        return [
            // 1) Clientes
            Card::make('Clientes', $fmt($totalClientes))
                ->description('Clientes registrados')
                ->descriptionIcon('heroicon-s-user-group')
                ->color('secondary')
                ->extraAttributes([
                    'class'   => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/clientes'",
                    'title'   => 'Ir a Clientes',
                ]),

            // 2) Cheques
            Card::make('Cheques pendientes', $fmt($chequesPendCant))
                ->description($pyg($chequesPendTotal))
                ->descriptionIcon($chequesPendCant > 0 ? 'heroicon-s-clock' : 'heroicon-s-check-circle')
                ->color($chequesPendCant > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'class'   => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/calendario-cheques'",
                    'title'   => 'Ir a Cheques',
                ]),

            // 3) 💰 Monto en Cajas Abiertas Hoy
            Card::make('Monto en Cajas Abiertas Hoy', $pyg($cajasAbiertasMonto))
                ->description($cajasAbiertasCant . ' caja(s) abiertas')
                ->descriptionIcon($cajasAbiertasCant > 0 ? 'heroicon-s-lock-open' : 'heroicon-s-minus-circle')
                ->color($cajasAbiertasCant > 0 ? 'success' : 'secondary')
                ->extraAttributes([
                    'class'   => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/cajas'",
                    'title'   => 'Ir a Cajas',
                ]),

            // 4) Transacciones
            Card::make('Transacciones (hoy)', $fmt($transHoyCant))
                ->description($pyg($transHoyTotal))
                ->descriptionIcon($transHoyCant > 0 ? 'heroicon-s-currency-dollar' : 'heroicon-s-minus-circle')
                ->color($transHoyCant > 0 ? 'success' : 'warning')
                ->extraAttributes([
                    'class'   => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/transacciones'",
                    'title'   => 'Ir a Transacciones',
                ]),

            // 5) Pedidos
            Card::make('Pedidos (hoy)', $fmt($pedidosHoy))
                ->description($pyg($pedidosHoyTotal))
                ->descriptionIcon($pedidosHoy > 0 ? 'heroicon-s-trending-up' : 'heroicon-s-minus-circle')
                ->color($pedidosHoy > 0 ? 'success' : 'warning')
                ->extraAttributes([
                    'class'   => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/pedidos'",
                    'title'   => 'Ir a Pedidos',
                ]),

            // 6) Ropas
            Card::make('Ropas vendidas (hoy)', $fmt($ropaHoyCant))
                ->description($pyg($ropaHoyTotal))
                ->descriptionIcon($ropaHoyCant > 0 ? 'heroicon-s-shopping-bag' : 'heroicon-s-minus-circle')
                ->color($ropaHoyCant > 0 ? 'success' : 'warning')
                ->extraAttributes([
                    'class'   => 'cursor-pointer',
                    'onclick' => "window.location.href='/admin/ropas'",
                    'title'   => 'Ir a Ropas',
                ]),
        ];
    }
}
