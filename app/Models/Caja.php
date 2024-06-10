<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = "caja";

    protected $fillable = [
        'fecha',
        'pedido_id',
        'poveedor_id',
        'descripcion',
        'importe',
        'metodo_pago',
        'tipo_movimiento',
        'banco',
        'estado',
        'nFactura',
        'nInterno',
        'iva',
        'descuento',
        'retencion',
        'fechaVencimiento',
        'fechaPago',
        'departamento',
        'delegacion_id',
        'cuenta',
        'documento_pdf',
        'importeIva',
        'total',
        'pagado',
        'pendiente',
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
