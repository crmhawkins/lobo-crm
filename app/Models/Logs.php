<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;
    protected $table = "logs";

    protected $fillable = [
        'action',
        'description',
        'date',
        'user_id',
        'reference',
        'logs_action_id',
        'pedido_id',
        'factura_id',
        'albaran_id',
        'caja_id',
        'cliente_id',
        'registroemail_id',
        'modificaciones_mercaderia_id',
        'modificaciones_stock_id',
        'producto_id',
        'proveedor_id',
        'rotura_stock_id',
        'servicios_facturas_id',
        'stock_id',
        'stock_entrante_id',
        'stock_registro_id',
        'user_create_id',

    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
