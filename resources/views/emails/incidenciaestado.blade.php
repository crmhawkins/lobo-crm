<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Estado de Incidencia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        /* Colores para los diferentes estados */
        .container.recibida {
            background-color: #E3E8EB;
            color: #333; /* Texto oscuro para buen contraste */
        }
        .container.tramite {
            background-color: #FFD700;
            color: #333; /* Texto oscuro para buen contraste */
        }
        .container.solucionada {
            background-color: #2ECC71;
            color: #ffffff; /* Texto blanco para mejor visibilidad */
        }
        .container.rechazada {
            background-color: #E74C3C;
            color: #ffffff; /* Texto blanco para mejor visibilidad */
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        /* Color del título basado en el estado */
        .recibida h2, .tramite h2 {
            color: #007bff; /* Azul para los estados claros */
        }
        .solucionada h2, .rechazada h2 {
            color: #ffffff; /* Texto blanco en estados con fondos oscuros */
        }
        p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        a {
            color: black !important;
            text-decoration: none !important;
            font-weight: bold;
        }
        .button {
            background-color: #FCB614;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 4px;
            text-align: center;
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            font-size: 16px;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container {{ $incidencia->estado }}">
    <h2>Cambio de Estado de Incidencia</h2>
    <p>Estimado/a {{ $empleado->name }} {{ $empleado->surname }},</p>

    <p>Le informamos que la incidencia {{ $type == 'pedido' ? 'de pedido' : '' }} a su cargo ha cambiado de estado.</p>

    <p><strong>Estado actual:</strong> {{ ucfirst($incidencia->estado) }}</p>
    <p><strong>Observaciones:</strong> {{ $incidencia->observaciones }}</p>

    @if ($type == 'pedido')
        <p><strong>Número de Pedido:</strong> {{ $incidencia->pedido_id }}</p>
    @endif

    <a href="{{ route('admin.incidencias.index') }}" class="button">Ver Incidencia</a>

    <p>Por favor, gestione esta incidencia lo antes posible.</p>

    <div class="footer">
        <p>Gracias</p>
    </div>
</div>

</body>
</html>
