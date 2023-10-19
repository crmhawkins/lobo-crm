<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = "alumnos";

    protected $fillable = [
        'nombre',
        'empresa_id',
        'apellidos',
        'dni',
        'fecha_nac',
        'direccion',
        'localidad',
        'provincia',
        'cod_postal',
        'pais',
        'telefono',
        'movil',
        'email',
        'created_at',
        'updated_at',
        'deleted_at',

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
