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
    <p>Total Caja: {{ number_format($record->total_caja, 0, '', '') }}</p><br>
    <p>Cantidad de Transacciones: {{ $record->cantidad_trx }}</p><br>
    <p>CE: {{ $record->contador_ediciones }}</p><br>
    <p>Cancelados: {{ $record->contador_cancelados }}</p>
</body>
</html>
