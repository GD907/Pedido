{{-- resources/views/RepartoPdF.blade.php --}}
@php
    // Trae SIEMPRE pedidos con su cliente (relación 'clientes' y campo 'nombre_comercio')
    $pedidos = method_exists($record, 'pedidos')
        ? $record->pedidos()->with('clientes:id,nombre_comercio')->get()
        : \App\Models\Pedido::with('clientes:id,nombre_comercio')
            ->where('reparto_id', $record->id)
            ->get();

    $cantidadBoletas = $pedidos->count();

    // Totales por método
    $calc = [
        'efectivo'      => (int) $pedidos->where('metodo_pago', 'efectivo')->sum('total_venta'),
        'transferencia' => (int) $pedidos->where('metodo_pago', 'transferencia')->sum('total_venta'),
        'tarjeta'       => (int) $pedidos->where('metodo_pago', 'tarjeta')->sum('total_venta'),
    ];

    $totalGeneral = (int) ($record->monto_total ?? array_sum($calc));

    // Formato guaraníes sin decimales
    function pyg($n) { return number_format((int)$n, 0, ',', '.'); }
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reparto #{{ $record->id }}</title>
    <style>
        html, body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .wrap { width: 100%; padding: 6px 8px; }
        .c { text-align: center; }
        .r { text-align: right; }
        .b { font-weight: bold; }
        .hr { border-top: 1px dashed #000; margin: 6px 0; }
        .sm { font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="c b">RESUMEN DE REPARTO</div>
    <div class="c sm">
        Nro de Reparto: {{ $record->id }}
        • {{ optional(\Carbon\Carbon::parse($record->fecha))->format('d/m/Y H:i') }}
    </div>

    <div class="hr"></div>

    <div><span class="b">Repartidor:</span> {{ optional($record->usuarioRepartidor)->name ?? '—' }}</div>
    <div><span class="b">Zona:</span> {{ $record->zona ?? '—' }}</div>
    <div><span class="b">Cantidad de boletas:</span> {{ $cantidadBoletas }}</div>

    <div class="hr"></div>

    <div class="b">Pedidos</div>
    <table>
        <tbody>
        @forelse ($pedidos as $p)
            <tr>
                <td style="width: 65%;">
                    {{ optional($p->clientes)->nombre_comercio
                        ?? optional($p->clientes)->nombre
                        ?? 'Cliente s/d' }}
                    <div class="sm">{{ \Carbon\Carbon::parse($p->fecha ?? $p->created_at)->format('d/m H:i') }}</div>
                </td>
                <td class="r" style="width: 35%;">Gs. {{ pyg($p->total_venta) }}</td>
            </tr>
        @empty
            <tr><td colspan="2" class="c sm">Sin pedidos asociados</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="hr"></div>

    <table>
        <tr>
            <td class="b">Efectivo</td>
            <td class="r">Gs. {{ pyg($record->total_efectivo ?? $calc['efectivo']) }}</td>
        </tr>
        <tr>
            <td class="b">Transferencia</td>
            <td class="r">Gs. {{ pyg($record->total_transferencia ?? $calc['transferencia']) }}</td>
        </tr>
        <tr>
            <td class="b">Tarjeta</td>
            <td class="r">Gs. {{ pyg($record->total_tarjeta ?? $calc['tarjeta']) }}</td>
        </tr>
        <tr>
            <td class="b">TOTAL</td>
            <td class="r b">Gs. {{ pyg($totalGeneral) }}</td>
        </tr>
    </table>

    <div class="hr"></div>
    <div class="c sm">Casa Nidia</div>
</div>
</body>
</html>
