<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cierre del Día - {{ $record->fecha_hora_cierre->format('d/m/Y H:i') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            letter-spacing: 2px;
            margin: 0;
        }
        .header-table {
            width: 90%;
            margin: 10px auto;
        }
        .header-table td {
            vertical-align: top;
            padding: 4px;
        }
        .left-header {
            text-align: left;
        }
        .right-header {
            text-align: right;
        }
        .right-header h1 {
            font-size: 14px;
            margin: 0;
        }
        .right-header p, .left-header p {
            font-size: 12px;
            margin: 2px 0;
        }
        table {
            width: 90%;
            margin: 10px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid transparent;
            padding: 5px;
            text-align: left;
        }
        .section-title {
            text-align: center;
            font-weight: bold;
            margin: 20px 0 10px;
            text-decoration: underline;
        }
        .centered {
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="left-header">
                <p><strong>Fecha y hora:</strong> {{ $record->fecha_hora_cierre->format('d/m/Y H:i') }}</p>
                <p><strong>Dinero inicial:</strong> {{ number_format($record->dinero_inicial, 0, ',', '.') }} Gs</p>
                <p><strong>Creado por:</strong> {{ $record->creador->name ?? '—' }}</p>
            </td>
            <td class="right-header">
                <h1>Casa Nidia</h1>
                <p>Pirity km 14 - (0985)731538</p>
                <p><strong>Cierre del Día</strong></p>
            </td>
        </tr>
    </table>

    {{-- Caja --}}
    <p class="section-title">Caja</p>
    <table>
        <tr>
            <td><strong>Número de Caja:</strong></td>
            <td>{{ $record->caja?->numero_caja ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>Total Efectivo:</strong></td>
            <td>{{ number_format($record->caja_efectivo, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Total Transferencia:</strong></td>
            <td>{{ number_format($record->caja_transferencia, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Total Tarjeta:</strong></td>
            <td>{{ number_format($record->caja_tarjeta, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Total Caja:</strong></td>
            <td>{{ number_format($record->caja_total, 0, ',', '.') }} Gs</td>
        </tr>
    </table>

    {{-- Reparto --}}
    <p class="section-title">Reparto</p>
    <table>
        <tr>
            <td><strong>Zona:</strong></td>
            <td>{{ $record->reparto?->zona ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>Total Efectivo:</strong></td>
            <td>{{ number_format($record->reparto_efectivo, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Total Transferencia:</strong></td>
            <td>{{ number_format($record->reparto_transferencia, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Total Tarjeta:</strong></td>
            <td>{{ number_format($record->reparto_tarjeta, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Total Reparto:</strong></td>
            <td>{{ number_format($record->reparto_total, 0, ',', '.') }} Gs</td>
        </tr>
    </table>

    {{-- Servicio Wepa --}}
    <p class="section-title">Servicio Wepa</p>
    <table>
        <tr>
            <td><strong>Lote:</strong></td>
            <td>{{ $record->wepa_lote }}</td>
        </tr>
        <tr>
            <td><strong>Ingresos:</strong></td>
            <td>{{ number_format($record->wepa_ingresos, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Egresos:</strong></td>
            <td>{{ number_format($record->wepa_egresos, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Cantidad de Operaciones:</strong></td>
            <td>{{ $record->wepa_cantidad }}</td>
        </tr>
        <tr>
            <td><strong>Comisión:</strong></td>
            <td>{{ number_format($record->wepa_comision, 0, ',', '.') }} Gs</td>
        </tr>
    </table>

    {{-- Servicio Aquipago --}}
    <p class="section-title">Servicio Aquipago</p>
    <table>
        <tr>
            <td><strong>Lote:</strong></td>
            <td>{{ $record->aquipago_lote }}</td>
        </tr>
        <tr>
            <td><strong>Ingresos:</strong></td>
            <td>{{ number_format($record->aquipago_ingresos, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Egresos:</strong></td>
            <td>{{ number_format($record->aquipago_egresos, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Cantidad de Operaciones:</strong></td>
            <td>{{ $record->aquipago_cantidad }}</td>
        </tr>
        <tr>
            <td><strong>Comisión:</strong></td>
            <td>{{ number_format($record->aquipago_comision, 0, ',', '.') }} Gs</td>
        </tr>
    </table>

    {{-- Totales Finales --}}
    <p class="section-title">Totales Finales</p>
    <table>
        <tr>
            <td><strong>Total Efectivo:</strong></td>
            <td>{{ number_format($record->total_efectivo, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Total Transferencia:</strong></td>
            <td>{{ number_format($record->total_transferencia, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Total Tarjeta:</strong></td>
            <td>{{ number_format($record->total_tarjeta, 0, ',', '.') }} Gs</td>
        </tr>
        <tr>
            <td><strong>Total General:</strong></td>
            <td><strong>{{ number_format($record->total_general, 0, ',', '.') }} Gs</strong></td>
        </tr>
    </table>

</body>
</html>
