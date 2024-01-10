<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento QR</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .qr-code {
            width: 50%; /* Ajusta el tamaño según tus necesidades */
            height: auto;
        }
    </style>
</head>
<body>
    <div class="qr-code">
        {!! $lote->codigo_qr !!}
    <p>Lote ID: {{ $lote->lote_id }}</p>
    <p>Cantidad: {{ $lote->cantidad }}</p>
    <p>Fecha de Entrada: {{ $lote->fecha_entrada }}</p>
    <!-- Agrega aquí más detalles según sea necesario -->
    </div>
</body>
</html>
