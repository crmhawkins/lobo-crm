<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidosIncidencias extends Model
{
    use HasFactory;

    protected $table = 'pedidos_incidencias';

    protected $fillable = [
        'pedido_id',
        'factura_id',
        'observaciones',
        'estado'
    ];
}
