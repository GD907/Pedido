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
</head>
<body>
    <p>Reporte de Caja N {{ $record->numero_caja }}</p><br>
    <p>Fecha y Hora: {{ $record->fecha }}</p><br>
    <p>Encargado: {{ $record->users->name }}</p><br>
    <p>Estado: {{ $record->estado }}</p><br>
    <p>----------------------------------------</p><br>
    <p class="text-center">Resumen por método de pago</p><br>

    <p>----------------------------------------</p><br>
    <p><strong>Transacciones ({{ $record->cantidad_trx ?? 0 }})</strong></p><br>
    <p>Efectivo: <span class="text-right">{{ number_format($record->total_trx_efectivo ?? 0, 0, '', '.') }} Gs</span></p><br>
    <p>Transferencia: <span class="text-right">{{ number_format($record->total_trx_transferencia ?? 0, 0, '', '.') }} Gs</span></p><br>
    <p>Tarjeta: <span class="text-right">{{ number_format($record->total_trx_tarjeta ?? 0, 0, '', '.') }} Gs</span></p><br>
    <p class="text-right">Total: {{ number_format($record->total_trx_general ?? 0, 0, '', '.') }} Gs</p><br>

    <p>----------------------------------------</p><br>
    <p><strong>Ropas ({{ $record->cantidad_ventas_ropa ?? 0 }})</strong></p><br>
    <p>Efectivo: <span class="text-right">{{ number_format($record->total_ropa_efectivo ?? 0, 0, '', '.') }} Gs</span></p><br>
    <p>Transferencia: <span class="text-right">{{ number_format($record->total_ropa_transferencia ?? 0, 0, '', '.') }} Gs</span></p><br>
    <p>Tarjeta: <span class="text-right">{{ number_format($record->total_ropa_tarjeta ?? 0, 0, '', '.') }} Gs</span></p><br>
    <p class="text-right">Total: {{ number_format($record->total_ropa_general ?? 0, 0, '', '.') }} Gs</p><br>

    <p>----------------------------------------</p><br>
    <p><strong>Boletas ({{ $record->cantidad_boletas ?? 0 }})</strong></p><br>
    <p>Efectivo: <span class="text-right">{{ number_format($record->total_boleta_efectivo ?? 0, 0, '', '.') }} Gs</span></p><br>
    <p>Transferencia: <span class="text-right">{{ number_format($record->total_boleta_transferencia ?? 0, 0, '', '.') }} Gs</span></p><br>
    <p>Tarjeta: <span class="text-right">{{ number_format($record->total_boleta_tarjeta ?? 0, 0, '', '.') }} Gs</span></p><br>
    <p class="text-right">Total: {{ number_format($record->total_boleta_general ?? 0, 0, '', '.') }} Gs</p><br>

    <p>----------------------------------------</p><br>
    <p><strong>Totales Generales por Método de Pago</strong></p><br>
    <p>Efectivo Total: <span class="text-right">
        {{
            number_format(
                ($record->total_trx_efectivo ?? 0) +
                ($record->total_ropa_efectivo ?? 0) +
                ($record->total_boleta_efectivo ?? 0), 0, '', '.'
            )
        }} Gs</span></p><br>
    <p>Transferencia Total: <span class="text-right">
        {{
            number_format(
                ($record->total_trx_transferencia ?? 0) +
                ($record->total_ropa_transferencia ?? 0) +
                ($record->total_boleta_transferencia ?? 0), 0, '', '.'
            )
        }} Gs</span></p><br>
    <p>Tarjeta Total: <span class="text-right">
        {{
            number_format(
                ($record->total_trx_tarjeta ?? 0) +
                ($record->total_ropa_tarjeta ?? 0) +
                ($record->total_boleta_tarjeta ?? 0), 0, '', '.'
            )
        }} Gs</span></p><br>

    <p class="text-right"><strong>Total Recaudado: {{ number_format($record->total_caja ?? 0, 0, '', '.') }} Gs</strong></p><br>
    <p>----------------------------------------</p><br>
</body>
</html>
