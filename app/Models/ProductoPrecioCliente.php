<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoPrecioCliente extends Model
{
    use HasFactory;

    protected $table = 'producto_precio_cliente';

    protected $fillable = [
        'producto_id',
        'cliente_id',
        'precio',
    ];

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Clients::class, 'cliente_id');
    }

    
}
