<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoLote extends Model
{
    use HasFactory;

    protected $table = "producto_lotes";

    protected $fillable = [
        'lote_id',
        'producto_id',
        'fecha_entrada',
        'cantidad_inicial',
        'cantidad_actual',
        'estado',
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
