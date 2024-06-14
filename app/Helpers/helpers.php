<?php
use App\Models\ChatGpt;

if (!function_exists('enviarMensajeWhatsApp')) {
    function enviarMensajeWhatsApp($template, $data, $buttondata, $telefono, $idioma = 'es') {
    $token = env('TOKEN_WHATSAPP', 'valorPorDefecto');

    $mensajePersonalizado = [
        "messaging_product" => "whatsapp",
        "recipient_type" => "individual",
        "to" => $telefono,
        "type" => "template",
        "template" => [
            "name" => $template,
            "language" => ["code" => $idioma],
            "components" => [],
        ],
    ];

    if (count($data) > 0) {
        $mensajePersonalizado['template']['components'][] = [
            "type" => "body",
            "parameters" => $data,
        ];
    }

    if (count($buttondata) > 0) {
        // Asegurarse de que cada parámetro de botón tenga el tipo 'text'
        $buttonParameters = [];
        foreach ($buttondata as $button) {
            $buttonParameters[] = [
                "type" => "text",
                "text" => $button, // Asegurarse de que $button es una cadena
            ];
        }

        $mensajePersonalizado['template']['components'][] = [
            "type" => "button",
            "sub_type" => "url",
            "index" => 0,
            "parameters" => $buttonParameters,
        ];
    }

    $urlMensajes = 'https://graph.facebook.com/v19.0/367491926438581/messages';

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlMensajes,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($mensajePersonalizado),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ),
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Obtener el código de estado HTTP

    curl_close($curl);
    $responseDecoded = json_decode($response, true);


    if($httpCode == 200 && isset($responseDecoded['messages'])){
        
        if($template == 'automatico_preparacion'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Pedido en preparación durante más de 2 días</strong><br>El pedido nº ".$data[0]['text']." lleva más de dos días en preparación.";
            $chatGpt->save();
        }

        if($template == 'automatico_envio'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Pedido en envió más de 5 días</strong><br>El pedido nº ".$data[0]['text']." leva más de 5 días en envio.";
            $chatGpt->save();
        }

        if($template == 'automatico_vencimiento'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Factura Vencimiento: En Tres Dias</strong><br>El pedido nº ".$data[0]['text']." vencera en 3 días.";
            $chatGpt->save();
        }

        if($template == 'pedido_albaran'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Estado del Pedido: Albarán</strong><br>Generado Albarán del pedido nº ".$data[0]['text'];
            $chatGpt->save();
        }

        if($template == 'stockaje_bajo'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Alerta de Stock Bajo</strong><br>Stock de ".$data[0]['text']." insuficiente en el almacen de ".$data[1]['text'];
            $chatGpt->save();
        }

        if($template == 'pedido_preparacion'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Estado del Pedido: Preparación</strong><br>El pedido nº ".$data[0]['text']." esta en preparación";
            $chatGpt->save();
        }

        if($template == 'pedido_ruta'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Estado del Pedido: En Ruta</strong><br>El pedido nº ".$data[0]['text']." esta en ruta";
            $chatGpt->save();
        }

        if($template == 'cliente_pendiente'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Revisión Pendiente: Nuevo Cliente</strong><br>Nuevo cliente a la espera de aprobación:".$data[0]['text'];
            $chatGpt->save();
        }

        if($template == 'pedido_entregado'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Estado del Pedido: Entregado</strong><br>El pedido nº ".$data[0]['text']." ha sido entregado";
            $chatGpt->save();
        }

        if($template == 'pedido_facturado'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Estado del Pedido: Facturado</strong><br>Se cobro el pedido nº ".$data[0]['text'];
            $chatGpt->save();
        }

        if($template == 'pedido_almacen'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Estado del Pedido: Aceptado en Almacén</strong><br>El pedido nº ".$data[0]['text']." ha sido aceptado";
            $chatGpt->save();
        }

        if($template == 'pedido_bloqueado'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Pedido Bloqueado: Pendiente de Aprobación</strong><br>El pedido nº ".$data[0]['text']." esta a la espera de aprobación";
            $chatGpt->save();
        }

        if($template == 'pedido_recibido'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Estado del Pedido: Recibido</strong><br>El pedido nº ".$data[0]['text']." ha sido recibido";
            $chatGpt->save();
        }

        if($template == 'pedido_rechazado'){
            $chatGpt = new ChatGpt();
            $chatGpt->id_mensaje = $responseDecoded['messages'][0]['id'];
            $chatGpt->remitente = $telefono;
            $chatGpt->respuesta = "<strong>Estado del Pedido: Rechazado</strong><br>El pedido nº ".$data[0]['text']." ha sido rechazado";
            $chatGpt->save();
        }

    }

    return $response;
}
}