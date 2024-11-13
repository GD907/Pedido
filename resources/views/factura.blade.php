<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura N° {{ $record->numero_factura }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* Ajuste de tamaño de fuente */
            letter-spacing: 2px; /* Espaciado entre letras */
            margin: 0;
        }
        .header-table {
            width: calc(60% + 1cm); /* Aumenta el ancho de la tabla en 1 cm */
            margin-left: auto;
            margin-bottom: 5px; /* Reducido el margen inferior */
        }
        .header-table td {
            vertical-align: top;
            padding: 3px;
        }
        .left-header {
            text-align: left;
        }
        .right-header {
            text-align: right;
        }
        .right-header h1 {
            font-size: 14px; /* Fuente para "Casa Nidia" */
            margin: 0;
        }
        .right-header p, .left-header p {
            font-size: 12px;
            margin: 3px 0;
        }
        table {
            width: 90%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-left: auto;
        }
        th, td {
            padding: 4px;
            text-align: left;
            border: 1px solid transparent;
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
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($record->fecha)->format('d/m/Y') }}</p>
                <p><strong>Cliente:</strong> {{ $record->clientes->nombre_comercio ?? ' ' }}</p>
                <p><strong>Encargado:</strong> {{ $record->users->name ?? 'Sin encargado' }}</p>
            </td>
            <td class="right-header">
                <h1>Casa Nidia</h1>
                <p>Pirity km 14 - (0985)731538</p>
                <p>Factura N° {{ $record->numero_factura }}</p>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="centered">Cantidad</th>
                <th>Descripción</th>
                <th class="centered">Precio Unitario</th>
                <th class="centered">Descuento(%)</th>
                <th class="centered">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->productos as $detalle)
            <tr>
                <td class="centered">{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->producto->nombre ?? 'Sin descripción' }}</td>
                <td class="centered">{{ number_format($detalle->precio, 0, ',', '.') }}</td>
                <td class="centered">{{ number_format($detalle->pordescuento, 0) }}%</td>
                <td class="centered">{{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                <td class="centered">{{ number_format($record->total_venta, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
