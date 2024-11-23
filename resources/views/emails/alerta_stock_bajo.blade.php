<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 15px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .title {
            color: #dc3545;
            margin-bottom: 15px;
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
        <img src="{{ asset('assets/images/logo_la_fabrica.png') }}" alt="SerLobo Logo" class="logo">
        <h2>Alerta de Stock Bajo</h2>
    </div>

    <div class="content">
        <h3 class="title">Stock Bajo: {{ $nombreMercaderia }}</h3>

        <p>Hola,</p>

        <p>La mercadería <strong>{{ $nombreMercaderia }}</strong> tiene un stock bajo.</p>

        <p>Por favor, revisa el inventario para tomar las acciones necesarias.</p>
    </div>

    <div class="footer">
        <p>Este es un correo automático, por favor no responder.</p>
        <p>© {{ date('Y') }} SerLobo. Todos los derechos reservados.</p>
    </div>
</body>
</html>