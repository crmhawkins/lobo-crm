<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Clients;
use App\Models\Facturas;

class Pedido extends Model
{
    use HasFactory;
    use SoftDeletes;
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cliente_id',
        'numero',
        'precio',
        'estado',
        'fecha',
        'descuento',
        'direccion_entrega',
        'provincia_entrega',
        'localidad_entrega',
        'cod_postal_entrega',
        'orden_entrega',
        'observaciones',
        'tipo_pedido_id',
        'almacen_id',
        'porcentaje_descuento',
        'bloqueado',
        'fecha_salida',
        'empresa_transporte',
        'subtotal',
        'iva_total',
        'descuento_total',
        'npedido_cliente',
        'gastos_envio',
        'transporte',
        'departamento_id',
        'fecha_entrega',
        'documento',
        'gastos_transporte',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
    public function cliente()
    {
        return $this->belongsTo(Clients::class, 'cliente_id');
    }

    //hasFactura
    public function factura()
    {
        return $this->hasOne(Facturas::class);
    }

    public function productosPedido()
    {
        return $this->hasMany(ProductosPedido::class, 'pedido_id');
    }

}
