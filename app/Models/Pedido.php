<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory;
    use SoftDeletes;
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cliente_id',
        'numero',
        'precio',
        'estado',
        'fecha',
        'descuento',
        'direccion_entrega',
        'provincia_entrega',
        'localidad_entrega',
        'cod_postal_entrega',
        'orden_entrega',
        'observaciones',
        'tipo_pedido_id',
        'almacen_id',
        'porcentaje_descuento',
        'bloqueado',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
