<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cursos extends Model
{
    use HasFactory;

    protected $table = "cursos";

    protected $fillable = [
        'nombre',
        'denominacion_id',
        'celebracion_id',
        'precio',
        'duracion',
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
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
