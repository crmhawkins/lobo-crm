<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosPedidoPack extends Model
{
    use HasFactory;
    
    protected $table = 'productos_pedido_pack';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'pack_id',
        'unidades',
        'lote_id',
    ];


    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id');
    }

    public function pack()
    {
        return $this->belongsTo(Productos::class, 'pack_id');
    }

    
}
