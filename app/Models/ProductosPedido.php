<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosPedido extends Model
{
    use HasFactory;

    protected $table = "productos_pedido";
    protected $fillable = [
        'producto_pedido_id',
        'pedido_id',
        'unidades',
        'precio_ud',
        'precio_total',
        'lote_id',
    ];
    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_pedido_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedidos::class, 'pedido_id');
    }
    
}
