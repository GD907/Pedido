<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cierre del Día</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .info {
            margin-bottom: 15px;
            text-align: center;
        }

        .info .fecha {
            font-size: 16px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .section-title {
            background-color: #ddd;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Resumen del Cierre del Día</h1>

    <div class="info">
        <p class="fecha">
            {{ ucfirst(\Carbon\Carbon::parse($record->fecha_hora_cierre)->translatedFormat('l d/m/Y H:i')) }}
        </p>
        <p><strong>Encargado:</strong> {{ $record->creador->name ?? '---' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Detalle</th>
                <th>Efectivo</th>
                <th>Transferencia</th>
                <th>Tarjeta</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dinero Inicial</td>
                <td>{{ number_format($record->dinero_inicial, 0, '', '.') }} Gs</td>
                <td>-</td>
                <td>-</td>
            </tr>

            <tr>
                <td class="section-title" colspan="4">Caja</td>
            </tr>
            <tr>
                <td>Total Caja</td>
                <td>{{ number_format($record->caja_efectivo, 0, '', '.') }} Gs</td>
                <td>{{ number_format($record->caja_transferencia, 0, '', '.') }} Gs</td>
                <td>{{ number_format($record->caja_tarjeta, 0, '', '.') }} Gs</td>
            </tr>

            <tr>
                <td class="section-title" colspan="4">Reparto</td>
            </tr>
            <tr>
                <td>Total Reparto</td>
                <td>{{ number_format($record->reparto_efectivo, 0, '', '.') }} Gs</td>
                <td>{{ number_format($record->reparto_transferencia, 0, '', '.') }} Gs</td>
                <td>{{ number_format($record->reparto_tarjeta, 0, '', '.') }} Gs</td>
            </tr>

            <tr>
                <td class="section-title" colspan="4">Aquipago - Lote: {{ $record->aquipago_lote ?? '---' }}</td>
            </tr>
            <tr>
                <td>Ingresos</td>
                <td>{{ number_format($record->aquipago_ingresos, 0, '', '.') }} Gs</td>
                <td colspan="2">-</td>
            </tr>
            <tr>
                <td>Egresos</td>
                <td>-{{ number_format($record->aquipago_egresos, 0, '', '.') }} Gs</td>
                <td colspan="2">-</td>
            </tr>
            <tr>
                <td>Cantidad de Extracciones de Tarjetas</td>
                <td colspan="3">{{ $record->aquipago_cantidad ?? 0 }}</td>
            </tr>
            <tr>
                <td>Comisión Total</td>
                <td>{{ number_format($record->aquipago_comision, 0, '', '.') }} Gs</td>
                <td colspan="2">-</td>
            </tr>

            <tr>
                <td class="section-title" colspan="4">Wepa - Lote: {{ $record->wepa_lote ?? '---' }}</td>
            </tr>
            <tr>
                <td>Ingresos</td>
                <td>{{ number_format($record->wepa_ingresos, 0, '', '.') }} Gs</td>
                <td colspan="2">-</td>
            </tr>
            <tr>
                <td>Egresos</td>
                <td>-{{ number_format($record->wepa_egresos, 0, '', '.') }} Gs</td>
                <td colspan="2">-</td>
            </tr>
            <tr>
                <td>Cantidad de Retiros Western Union</td>
                <td colspan="3">{{ $record->wepa_cantidad ?? 0 }}</td>
            </tr>
            <tr>
                <td>Comisión Total</td>
                <td>{{ number_format($record->wepa_comision, 0, '', '.') }} Gs</td>
                <td colspan="2">-</td>
            </tr>

            <tr>
                <td class="section-title" colspan="4">Totales Finales</td>
            </tr>
            <tr>
                <td><strong>Total Efectivo</strong></td>
                <td><strong>{{ number_format($record->total_efectivo, 0, '', '.') }} Gs</strong></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td><strong>Total Transferencia</strong></td>
                <td></td>
                <td><strong>{{ number_format($record->total_transferencia, 0, '', '.') }} Gs</strong></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>Total Tarjeta</strong></td>
                <td></td>
                <td></td>
                <td><strong>{{ number_format($record->total_tarjeta, 0, '', '.') }} Gs</strong></td>
            </tr>
            <tr>
                <td><strong>Total General</strong></td>
                <td colspan="3"><strong>{{ number_format($record->total_general, 0, '', '.') }} Gs</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
