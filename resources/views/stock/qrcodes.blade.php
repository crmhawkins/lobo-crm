<!DOCTYPE html>
<html>

<head>
    <title>Códigos QR para asignación de stock (Productos)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center; /* Alinear el texto al centro */
        }
        .container {
            margin: 20px auto; /* Centrar horizontalmente el contenedor */
            padding: 20px;
            max-width: 800px; /* Ajustar según tus necesidades */
        }
        .qr-code {
            display: inline-flex;
            margin: 10px;
            justify-content: center;
            align-items: center;
            width: 300px; /* Ajustar según tus necesidades */
            height: 300px; /* Ajustar según tus necesidades */
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
