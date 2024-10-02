<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PedidosIncidencias extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'pedidos_incidencias';

    protected $fillable = [
        'pedido_id',
        'factura_id',
        'observaciones',
        'estado',
        'user_id',
        'notas',  // Añadir este campo

    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
