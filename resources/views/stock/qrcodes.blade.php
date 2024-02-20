<!DOCTYPE html>
<html>

<head>
    <title>Códigos QR para asignación de stock (Productos)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            margin: 0 auto;
            padding: 20px;
        }
        .qr-code {
            display: inline-flex;
            margin: 10px;
            justify-content: center;
            align-items: center;
            width: 300px; /* Ajusta este valor para cambiar el tamaño de los QR */
            height: 300px; /* Ajusta este valor para cambiar el tamaño de los QR */
        }
        img {
            max-width: 100%;
            max-height: 100%;
        }
        @page {
            margin: 10mm;
        }
        h2 {
            margin-bottom: 40px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Códigos QR para asignación de stock (Productos)</h2>
        @foreach ($qrcodes as $qr)
            <div class="qr-code">
                <img src="data:image/png;base64, {!! base64_encode($qr) !!}">
            </div>
        @endforeach
    </div>
</body>

</html>
