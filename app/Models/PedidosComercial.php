<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PedidosComercial extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "pedidos_comercial"; 
    protected $fillable = [
        'npedido',
        'cliente_id',
        'comercial_id',
        'precio',
        'subtotal',
        'iva',
        'total',
        'direccion_entrega',
        'localidad_entrega',
        'cod_postal_entrega',
        'provincia_entrega',
        'observaciones',
        'fecha',
    ];


    public function cliente()
    {
        return $this->belongsTo(ClientesComercial::class, 'cliente_id');
    }

    public function productos()
    {
        return $this->hasMany(ProductosPedido::class, 'pedido_id');
    }

    public function comercial()
    {
        return $this->belongsTo(User::class, 'comercial_id');
    }


    
}
