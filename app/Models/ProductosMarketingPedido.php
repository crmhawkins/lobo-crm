<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosMarketingPedido extends Model
{
    use HasFactory;

    protected $table = "productos_marketing_pedido";
    protected $fillable = [
        'pedido_id',
        'producto_marketing_id',
        'unidades',
        'precio_ud',
        'precio_total',
    ];

    public function producto()
    {
        return $this->belongsTo(ProductosMarketing::class, 'producto_marketing_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedidos::class, 'pedido_id');
    }

    
}
