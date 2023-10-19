<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = "empresas";

    protected $fillable = [
        'nombre',
        'telefono1',
        'telefono2',
        'direccion',
        'cif',
        'email',
        'cod_postal',
        'localidad',
        'pais',
        'legal1',
        'legal2',
        'legal3',
        'legal4',

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
