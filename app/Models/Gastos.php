<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gastos extends Model
{
    use HasFactory;

    protected $table = "gastos";

    protected $fillable = [
        'tipo_gasto',
        'nombre_gasto',
        'date',
        'cuantia',
        'repeticion',
        'activo',
        'created_at',
        'updated_at',
        'deleted_at',

    ];
}
