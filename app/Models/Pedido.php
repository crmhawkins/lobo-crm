<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cliente_id',
        'nombre',
        'precio',
        'estado',
        'fecha',
        'direccion_entrega',
        'provincia_entrega',
        'localidad_entrega',
        'cod_postal_entrega',
        'orden_entrega',
        'observaciones',
        'tipo_pedido_id',
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
