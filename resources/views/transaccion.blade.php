<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    /* Fuente y tamaño uniforme para tickets */
    * {
        font-family: "Courier New", Courier, monospace;
        font-size: 8px;
        margin: 3px;
        display: block;
    }
    p {
        font-family: "Courier New", Courier, monospace;
        font-size: 8px;
        margin: 2px 0;
        padding: 0;
        line-height: 1.2;
        word-spacing: 2px;
        white-space: pre;
        text-align: left;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    td {
        border: none;
        padding: 0;
        font-size: 8px;
    }
</style>

<span class="text-right">Casa Nidia - 0986 454 150</span> <br>
<p>{{  $record->numero_trx }}</p><br>
<p>Fecha: {{ $record->fecha }}</p><br>
<p>Cliente: {{ $record->clientes->nombre_comercio ?? ' ' }}</p><br>
<p>Encargado: {{ $record->users->name ?? 'Sin encargado' }}</p><br>
<p>----------------------------------------</p><br>
<p>Detalle </p><br>
<p>----------------------------------------</p><br>
@foreach($record->productos as $detalle)
<p>{{ $detalle->producto->nombre ?? 'Sin descripción' }}</p><br>
<p>Cant: {{ $detalle->cantidad }}</p><br>
<p class="text-right">Precio Unit: {{ number_format($detalle->precio, 0, ',', '.') }}</p><br>
<p class="text-right">Subtotal: {{ number_format($detalle->subtotal, 0, ',', '.') }}</p><br>
@endforeach
<p>----------------------------------------</p><br>
<span class="text-right">Total: {{ number_format($record->total_trx, 0, ',', '.') }} Gs.  </span>
<p>----------------------------------------</p><br>
<p class="text-center">Ticket Sin Validez Fiscal</p><br>
<p class="text-center">Gracias Por Su Compra</p><br>

