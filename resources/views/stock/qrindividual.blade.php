<!DOCTYPE html>
<html>

<head>
    <title>Códigos QR para asignación de stock (Productos)</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%; /* Hacer que el body ocupe toda la altura de la página */
            display: flex;
            justify-content: center; /* Centrar horizontalmente */
            align-items: center; /* Centrar verticalmente */
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center; /* Alinear el texto al centro */
            padding: 20px;
            width: 50%; /* Hacer el contenedor un poco más ancho */
            max-width: 500px; /* Ajustar según tus necesidades, esto hará el QR más grande */
        }
        .qr-code {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%; /* Hacer que el QR use todo el ancho del contenedor */
            padding: 10px; /* Espacio alrededor del QR */
        }
        img {
            width: 100%; /* Hacer que la imagen del QR sea tan grande como su contenedor */
            height: auto; /* Mantener la proporción de la imagen */
        }
        @page {
            margin: 20mm; /* Ajustar margen de la página si se va a imprimir */
        }
        h2 {
            margin-bottom: 40px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Código QR para asignación de stock (Productos)</h2> <!-- Agregué un título para más contexto -->
        <div class="qr-code">
            <img src="data:image/png;base64, {!! base64_encode($Qrcode) !!}">
        </div>
    </div>
</body>

</html>
