<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura N° {{ $record->numero_factura }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            letter-spacing: normal;
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
    <h1 class="header">Proveedor: {{ $record->proveedor }}</h1>
    <p><strong>Fecha:</strong> {{ $record->fecha }}</p>
    <p><strong>Encargado:</strong> {{ $record->users->name ?? 'Sin encargado' }}</p>
    <p><strong>Tipo de Entrada:</strong> {{ $record->tipo_entrada ?? 'Sin estado' }}</p>
    <p><strong>Observaciones:</strong> {{ $record->observacion ?? 'Sin observaciones' }}</p>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>P.Costo</th>
                <th>P.Pedido</th>
                <th>P.Transaccion</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->productos as $detalle)
            <tr>
                <td>{{ $detalle->producto->nombre ?? 'Sin descripción' }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>{{ number_format($detalle->preciocompra, 0) }}</td> <!-- Sin decimales -->
                <td>{{ number_format($detalle->precioventa, 0) }}</td> <!-- Sin decimales -->
                <td>{{ number_format($detalle->preciotransaccion, 0) }}</td> <!-- Sin decimales -->
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
