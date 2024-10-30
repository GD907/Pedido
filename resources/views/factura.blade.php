<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura N° {{ $record->numero_factura }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid transparent; /* Bordes transparentes */
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .header {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1 class="header">Factura N° {{ $record->numero_factura }}</h1>
    <p><strong>Fecha:</strong> {{ $record->fecha }}</p>
    <p><strong>Cliente:</strong> {{ $record->clientes->nombre_comercio ?? ' ' }}</p>
    <p><strong>Encargado:</strong> {{ $record->users->name ?? 'Sin encargado' }}</p>
    <p><strong>Estado del Pedido:</strong> {{ $record->estado_pedidos->nombre ?? 'Sin estado' }}</p>
    <p><strong>Observaciones:</strong> {{ $record->observacion ?? 'Sin observaciones' }}</p>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Porcentaje de Descuento</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->productos as $detalle)
            <tr>
                <td>{{ $detalle->producto->nombre ?? 'Sin descripción' }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>{{ number_format($detalle->precio, 0) }}</td> <!-- Sin decimales -->
                <td>{{ number_format($detalle->pordescuento, 0) }}%</td> <!-- Sin decimales -->
                <td>{{ number_format($detalle->subtotal, 0) }}</td> <!-- Sin decimales -->
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Total Venta:</strong></td>
                <td>{{ number_format($record->total_venta, 0) }}</td> <!-- Sin decimales -->
            </tr>
        </tfoot>
    </table>
</body>
</html>
