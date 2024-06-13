<?php
if (!function_exists('enviarMensajeWhatsApp')) {
    function enviarMensajeWhatsApp($template, $data, $telefono, $idioma = 'es') {
        $token = env('TOKEN_WHATSAPP', 'valorPorDefecto');

        if (count($data) > 0) {
            $mensajePersonalizado = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $telefono,
                "type" => "template",
                "template" => [
                    "name" => $template,
                    "language" => ["code" => $idioma],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => $data,
                        ],
                    ],
                ],
            ];
        } else {
            $mensajePersonalizado = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $telefono,
                "type" => "template",
                "template" => [
                    "name" => $template,
                    "language" => ["code" => $idioma],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [],
                        ],
                    ],
                ],
            ];
        }

        $urlMensajes = 'https://graph.facebook.com/v16.0/102360642838173/messages';

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