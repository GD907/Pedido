<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    /* Fuente y tamaño uniforme para tickets */
    * {
        font-family: "Courier New", Courier, monospace; /* Fuente monoespaciada para tickets */
        font-size: 8px; /* Tamaño de fuente uniforme */
        margin: 3px;
        display: block; /* Asegurar que cada línea ocupe su propio espacio */
    }
    p {
        font-family: "Courier New", Courier, monospace; /* Fuente monoespaciada para tickets */
        font-size: 8px; /* Tamaño de fuente uniforme */
        margin: 2px 0; /* Reducir márgenes para compactar */
        padding: 0;
        line-height: 1.2; /* Espaciado entre líneas */
        word-spacing: 2px; /* Espaciado entre palabras */
        white-space: pre; /* Respetar todos los espacios */
        text-align: left;
    }
    .text-right {
        font-family: "Courier New", Courier, monospace; /* Fuente monoespaciada para tickets */
        font-size: 8px; /* Tamaño de fuente uniforme */
        margin: 2px 0; /* Reducir márgenes para compactar */
        padding: 0;
        line-height: 1.2; /* Espaciado entre líneas */
        word-spacing: 2px; /* Espaciado entre palabras */
        white-space: pre; /* Respetar todos los espacios */
        text-align: right; /* Alineación a la derecha */
    }
    table {
        width: 100%;
        border-collapse: collapse; /* Eliminar espacios entre celdas */
    }
    td {
        border: none; /* Hacer invisibles los bordes */
        padding: 0;
        font-size: 8px;
    }
    .text-left {
        text-align: left;
    }
    .text-right {
        text-align: right;
    }
</style>
<span class="text-right">Casa Nidia - 0986 454 150</span> <br>
<p>{{  $record->numero_trx }}</p><br>
<p>Fecha: {{ $record->fecha }}</p><br>
<p>Cliente: {{ $record->clientes->nombre_comercio ?? ' ' }}</p><br>
<p>Encargado: {{ $record->users->name ?? 'Sin encargado' }}</p><br>
<p>----------------------------------------</p><br>
<p>Total Venta: {{ number_format($record->total_trx, 0, ',', '.') }} </p><br>
<p>----------------------------------------</p><br>
@foreach($record->productos as $detalle)
<p>{{ $detalle->producto->nombre ?? 'Sin descripción' }}</p><br>
<p>Cant: {{ $detalle->cantidad }}</p><br>
<p class="text-right">Precio Unit: {{ number_format($detalle->precio, 0, ',', '.') }}</p><br>
<p class="text-right">Subtotal: {{ number_format($detalle->subtotal, 0, ',', '.') }}</p><br>
@endforeach
<p>----------------------------------------</p><br>
<span class="text-right">Total Venta: {{ number_format($record->total_trx, 0, ',', '.') }} </span>
