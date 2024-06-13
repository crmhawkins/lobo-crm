<?php
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
                    "text" => $button, // Suponiendo que $buttondata contiene URLs o textos necesarios
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
        curl_close($curl);
    
        return $response;
    }
}