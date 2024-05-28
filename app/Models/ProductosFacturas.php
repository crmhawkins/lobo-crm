<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosFacturas extends Model
{
    use HasFactory;

    protected $table = 'productos_factura';

    protected $fillable = [
        'factura_id',
        'producto_id',
        'cantidad',
        'unidades',
        'precio_ud',
        'total',
        'stock_entrante_id'
    ];

    public function factura()
    {
        return $this->belongsTo(Facturas::class, 'factura_id');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id');
    }

    public function stockEntrante()
    {
        return $this->belongsTo(StockEntrante::class, 'stock_entrante_id');
    }

    
}
