<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidencia Asignada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        h2 {
            color: #007bff;
            font-size: 24px;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        a {
            color:  black !important;
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
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Nueva Incidencia Asignada</h2>
    <p>Estimado/a {{ $empleado->name }} {{ $empleado->surname }},</p>

    <p>Se le ha asignado una nueva incidencia {{ $type == 'pedido' ? 'de pedido' : '' }}.</p>
    
    <p><strong>Estado:</strong> {{ ucfirst($incidencia->estado) }}</p>
    <p><strong>Observaciones:</strong> {{ $incidencia->observaciones }}</p>
    
    @if ($type == 'pedido')
        <p><strong>Número de Pedido:</strong> {{ $incidencia->pedido_id }}</p>
    @endif

    <a href="{{ route('admin.incidencias.index') }}">Ver Incidencia</a>

    <p>Por favor, gestione esta incidencia lo antes posible.</p>

    <p>Gracias</p>
</body>
</html>
