<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosMarketingPedidoPack extends Model
{
    use HasFactory;

    protected $table = 'productos_marketing_pedido_pack';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'pack_id',
        'unidades',
        'lote_id'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedidos::class, 'pedido_id');
    }

    public function producto()
    {
        return $this->belongsTo(ProductosMarketing::class, 'producto_id');
    }

    public function pack()
    {
        return $this->belongsTo(Productos::class, 'pack_id');
    }
}
