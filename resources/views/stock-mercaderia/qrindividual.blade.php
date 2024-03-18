<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Códigos QR para asignación de stock (Materiales)</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            padding: 20px;
            width: 100%;
        }
        .qr-code {
            margin: auto; /* Centra el .qr-code en el .container si es necesario */
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50%;
            height: auto; /* Ajuste para mantener la proporción de la imagen */
        }
        img {
            max-width: 100%;
            height: auto; /* Mantiene la proporción de la imagen */
        }
        @page {
            margin: 20mm;
        }
        h2 {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Código QR para asignación de stock (Materiales)</h2>
        <div class="qr-code">
            <img src="data:image/png;base64, {!! base64_encode($Qrcode) !!}" alt="Código QR">
        </div>
    </div>
</body>
</html>
