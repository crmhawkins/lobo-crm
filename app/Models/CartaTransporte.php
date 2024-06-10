<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartaTransporte extends Model
{
    use HasFactory;

    protected $table = 'carta_transporte';

    protected $fillable = [
        'remitente',
        'cargador_contractual',
        'operador_transporte',
        'consignatario',
        'lugar_entrega',
        'lugar_fecha_carga',
        'documentos_anexos',
        'marca_numeros',
        'numero_bultos',
        'clases_embalaje',
        'naturaleza',
        'n_estadistico',
        'peso_bruto',
        'volumen',
        'instrucciones',
        'forma_pago',
        'firma_transportista',
        'vehiculo',
        'porteadores_sucesivos',
        'reembolso',
        'lugar',
        'fecha',
        'precio_remitente',
        'liquido_remitente',
        'suplementos_remitente',
        'gastos_remitente',
        'precio_moneda',
        'liquido_moneda',
        'suplementos_moneda',
        'gastos_moneda',
        'precio_consignatario',
        'liquido_consignatario',
        'suplementos_consignatario',
        'gastos_consignatario',
        'total_remitente',
        'total_consignatario',
        'total_moneda',
        'pedido_id',
        'lugar_entrega_16',
        'formalizado',
        'remitente_tabla',
        'moneda_tabla',
        'consignatario_tabla',
        'descuento_remitente',
        'descuento_consignatario',
        'descuento_moneda',
        'lugar_entrega_22',
        'porte_pagado',
        'porte_debido',
        'remitente_1',

    ];


    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }


}
