<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    use HasFactory;

    protected $table = "proveedores";

    protected $attributes = [

    ];

    protected $fillable = [
        "nombre",
        "dni_cif",
        "direccion",
        "provincia",
        "localidad",
        "cod_postal",
        "telefono",
        "email",
        "nota",
        "cuenta_contable",
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
