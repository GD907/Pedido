<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Cajas;
use Carbon\Carbon;

class PedidoChart extends LineChartWidget
{
    protected static ?string $heading = 'Últimas 12 Cajas';

    protected function getData(): array
    {
        // 1) Últimas 11 cajas cerradas (orden cronológico)
        $cerradas = Cajas::query()
            ->where('estado', 'cerrado')
            ->orderByDesc('id')
            ->limit(11)
            ->get(['numero_caja', 'estado', 'total_caja', 'fecha', 'cierre'])
            ->reverse()
            ->values();

        // 2) Última caja abierta (si existe)
        $abierta = Cajas::query()
            ->where('estado', 'abierto')
            ->orderByDesc('id')
            ->first(['numero_caja', 'estado', 'total_caja', 'fecha', 'cierre']);

        // 3) Serie final: cerradas + abierta
        $series = collect($cerradas);
        if ($abierta) {
            $series->push($abierta);
        }

        // 4) Fallback si no hay datos
        if ($series->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Total por caja (Gs)',
                        'data'  => [0],
                    ],
                ],
                'labels' => ['Sin datos'],
            ];
        }

        // 5) Labels solo con día y fecha
        $labels = $series->map(function ($caja) {
            $fechaStr = $caja->cierre ?? $caja->fecha ?? null;
            return $fechaStr
                ? Carbon::parse($fechaStr)->locale('es')->translatedFormat('D d/m')
                : '—';
        })->toArray();

        // 6) Datos
        $data = $series->map(fn ($caja) => (int) ($caja->total_caja ?? 0))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total por caja (Gs)',
                    'data'  => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
