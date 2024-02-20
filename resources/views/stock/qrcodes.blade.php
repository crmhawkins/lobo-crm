<!DOCTYPE html>
<html>

<head>
    <title>Códigos QR para asignación de stock (Productos)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            text-align: center;
            margin: 20px auto; /* Ajusta este valor para el margen exterior */
            padding: 20px;
            max-width: 800px; /* Ajusta este valor según tus necesidades */
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .qr-code {
            margin: 10px;
            width: calc(50% - 20px); /* Ajusta este valor para cambiar el espacio entre los códigos QR */
            max-width: 180px; /* Ajusta este valor para limitar el ancho máximo de los códigos QR */
            height: auto; /* Para mantener la proporción */
        }
        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto; /* Centra la imagen horizontalmente */
        }
        @page {
            margin: 10mm;
            size: A4 landscape; /* Cambiar a A4 landscape para orientación horizontal */
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

