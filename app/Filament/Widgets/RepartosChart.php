<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Repartos;
use Carbon\Carbon;

class RepartosChart extends LineChartWidget
{
    protected static ?string $heading = 'Últimos 12 Repartos';

    protected function getData(): array
    {
        // Últimos 12 repartos (por id desc) y luego en orden cronológico
        $repartos = Repartos::query()
            ->orderByDesc('id')
            ->limit(12)
            ->get([
                'id',
                'fecha',
                'monto_total',
                'total_efectivo',
                'total_transferencia',
                'total_tarjeta',
                'created_at',
            ])
            ->reverse()
            ->values();

        // Fallback si no hay datos
        if ($repartos->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Total por reparto (Gs)',
                        'data'  => [0],
                    ],
                ],
                'labels' => ['Sin datos'],
            ];
        }

        // Labels: solo día y fecha (es)
        $labels = $repartos->map(function ($rep) {
            $fechaStr = $rep->fecha ?? $rep->created_at ?? null;
            return $fechaStr
                ? Carbon::parse($fechaStr)->locale('es')->translatedFormat('D d/m')
                : '—';
        })->toArray();

        // Datos principales: monto_total
        $data = $repartos->map(fn ($rep) => (int) ($rep->monto_total ?? 0))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total por reparto (Gs)',
                    'data'  => $data,
                ],

                // Si deseas mostrar también el desglose por método de pago, descomenta estas series:
                // [
                //     'label' => 'Efectivo (Gs)',
                //     'data'  => $repartos->map(fn ($rep) => (int) ($rep->total_efectivo ?? 0))->toArray(),
                // ],
                // [
                //     'label' => 'Transferencia (Gs)',
                //     'data'  => $repartos->map(fn ($rep) => (int) ($rep->total_transferencia ?? 0))->toArray(),
                // ],
                // [
                //     'label' => 'Tarjeta (Gs)',
                //     'data'  => $repartos->map(fn ($rep) => (int) ($rep->total_tarjeta ?? 0))->toArray(),
                // ],
            ],
            'labels' => $labels,
        ];
    }
}
