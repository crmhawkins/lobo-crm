<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Productos extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "productos";

    protected $fillable = [
        'nombre',
        'tipo_precio',
        'unidades_por_caja',
        'cajas_por_pallet',
        'foto_ruta',
        'descripcion',
        'materiales',
        'medidas_botella',
        'peso_neto_unidad',
        'temp_conservacion',
        'caducidad',
        'ingredientes',
        'alergenos',
        'proceso_elaboracion',
        'info_nutricional',
        'grad_alcohol',
        'domicilio_fabricante',
        'stock_seguridad',
        'precio'

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
