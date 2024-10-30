<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    /* Fuente y tamaño uniforme para tickets */
    * {
        font-family: "Courier New", Courier, monospace; /* Fuente monoespaciada para tickets */
        font-size: 8px; /* Tamaño de fuente uniforme */
        margin: 3px;
        /* padding: 1px; */
        /* line-height: 1.0; Espaciado entre líneas */
        /* word-spacing: 1px; Agrega 2px de espacio extra entre palabras */
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
        display: block; /* Asegurar que cada línea ocupe su propio espacio */
    }


</style>

Trx:{{  $record->numero_trx }}<br>
    ----------------------------------------
Fecha: {{ $record->fecha }}<br>
este' 'es un comentario de prueba<br>
este' 'es un comentario de prueba<br>
<p>Cliente: {{ $record->clientes->nombre_comercio ?? ' ' }}</p><br>
<p>Encargado: {{ $record->users->name ?? 'Sin encargado' }}</p><br>
    @foreach($record->productos as $detalle)
           <p> {{ $detalle->producto->nombre ?? 'Sin descripción' }} </p><br>
           <p> Cant: {{ $detalle->cantidad }} </p><br>
           <p> P. Unit: {{ number_format($detalle->precio, 0) }}</p><br>
           <p> Subtotal: {{ number_format($detalle->subtotal, 0) }}</p><br>
    @endforeach
   <p>  Total Venta: {{ number_format($record->total_trx, 0) }} </p><br>
