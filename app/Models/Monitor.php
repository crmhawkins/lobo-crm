<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    use HasFactory;

    protected $table = "monitores";

    protected $fillable = [
        'nombre',
        'apellidos',
        'fechaNa',
        'localidad',
        'telefono',
        'alias',
        'dni',
        'num_ss',
        'nivel_estudios',
        'domicilio',
        'provincia',
        'codigo_postal',
        'nombre_padre',
        'nombre_madre',
        'email',
        'created_at',
        'updated_at',
        'deleted_at',

    ];

    public function programas()
    {
        return $this->hasMany('app\Models\Programa');
    }


    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
