<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Incidencia</title>
</head>
<body>
    <h1>Estimado/a {{ $empleado->name }},</h1>
    <p>Le recordamos que tiene una incidencia {{ $type === 'normal' ? '' : 'de pedido' }}  pendiente de revisión.</p>
    <p><strong>Observaciones:</strong> {{ $incidencia->observaciones }}</p>
    @if($incidencia->notas)
        <p><strong>Notas adicionales:</strong> {{ $incidencia->notas }}</p>
    @endif
    <p>Le solicitamos que revise y actualice el estado de la incidencia a la mayor brevedad posible.</p>
    <p>Agradecemos su atención a este asunto.</p>
    <p>Atentamente,</p>
    <p>El equipo de gestión de incidencias</p>
</body>
</html>
