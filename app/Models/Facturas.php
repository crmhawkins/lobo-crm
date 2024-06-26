<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facturas extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "facturas";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero_factura',
        'pedido_id',
        'fecha_emision',
        'fecha_vencimiento',
        'descripcion',
        'estado',
        'metodo_pago',
        'cliente_id',
        'precio',
        'producto_id',
        'cantidad',
        'descuento',
        'tipo',
        'iva',
        'total',
        'subtotal_pedido',
        'iva_total_pedido',
        'descuento_total_pedido',
        'recargo',
        'total_recargo',
        'gastos_envio',
        'transporte',
        'anotacionesEmail',
        'observacionesEmail',
    ];


    public function cliente()
    {
        return $this->belongsTo(Clients::class, 'cliente_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
