<?php

namespace App\Helpers;

use App\Models\Facturas;
use App\Models\Clients;
use App\Models\Delegacion;

class FacturaHelper
{
    // Método no estático
    public function getDelegacion($clienteId)
    {
        $cliente = Clients::find($clienteId);
        if ($cliente) {
            $delegacion = $cliente->delegacion_COD;
            if ($delegacion) {
                $delegacion = Delegacion::where('COD', $delegacion)->first();
                if ($delegacion) {
                    return $delegacion->nombre;
                }
            }
        }

        return "No definido";
    }

    // Método estático para verificar si tiene IVA
    public static function facturaHasIva($facturaId)
    {
        // Obtener la factura
        $factura = Facturas::find($facturaId);
        
        if (!$factura) {
            return false; // O cualquier manejo de error
        }

        // Instanciar la clase para usar el método no estático
        $helper = new self();
        $delegacion = $helper->getDelegacion($factura->cliente_id);
        // Validar si la delegación está exenta de IVA
        if (in_array($delegacion, ['07 CANARIAS', '13 GIBRALTAR', '14 CEUTA', '15 MELILLA', '01.1 ESTE – SUR EXTERIOR' , '08 OESTE - INSULAR'])) {
            return false;
        }

        return true;
    }
}
