<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Evento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f9;
        }
        .header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Notificación de Evento</h1>
    </div>

    <div class="content">
        <h2>{{ $title }}</h2>
        <p><strong>Descripción:</strong> {{ $location }}</p>
        <p><strong>Inicio:</strong> {{ $start }}</p>
        <p><strong>Fin:</strong> {{ $end }}</p>
    </div>

    <div class="footer">
        <p>Este es un correo automático, por favor no responder.</p>
        <p>© {{ date('Y') }} SerLobo. Todos los derechos reservados.</p>
    </div>
</body>
</html>
