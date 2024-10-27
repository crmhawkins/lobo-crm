<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosPedidoComercial extends Model
{
    use HasFactory;

    protected $table = "productos_pedido_comercial";
    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_ud',
        'precio_total',
    ];

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id');
    }

    public function pedido()
    {
        return $this->belongsTo(PedidosComercial::class, 'pedido_id');
    }


}
