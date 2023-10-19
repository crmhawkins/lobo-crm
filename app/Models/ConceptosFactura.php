<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConceptosFactura extends Model
{
    use HasFactory;

    protected $table = "conceptos_facturas";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_factura',
        'id_producto',
        'cantidad',
        'iva',
        'descuento',
        'precio',
        'total',
        'total_sin_iva'
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
