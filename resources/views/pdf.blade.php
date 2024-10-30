<!-- resources/views/pdf.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Caja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Reporte de Caja N° {{ $record->numero_caja }}</h1><br>
    <p>Fecha y Hora: {{ $record->fecha }}</p><br>
    <p>Encargado: {{ $record->users->name }}</p><br>
    <p>Estado: {{ $record->estado }}</p><br>
    <p>Total Caja: {{ number_format($record->total_caja, 0, '', '') }}</p><br>
    <p>Cantidad de Transacciones: {{ $record->cantidad_trx }}</p><br>
    <p>CE:{{ $record->contador_ediciones }}</p><br>
    <p>Cancelados:{{ $record->contador_cancelados }}</p>
    <!-- Aquí puedes agregar más detalles según sea necesario -->
</body>
</html>
