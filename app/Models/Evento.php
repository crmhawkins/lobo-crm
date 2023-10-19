<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $table = "eventos";

    protected $fillable = [
        'eventoNombre',
        'eventoProtagonista',
        'eventoNiños',
        'eventoAdulto',
        //'id_contacto',
        'eventoContacto',
        'eventoParentesco',
        'eventoTelefono',
        'eventoLugar',
        'eventoLocalidad',
        'eventoMontaje',
        'diaEvento',
        'diaFinal',
        'created_at',
        'updated_at',
        'deleted_at',

    ];


    // public function contacto(){
    //     return $this->belongsTo(Cliente::class, "id_contacto");
    // }

    public function programas()
    {
        return $this->hasMany('app\Models\Programa');
    }

    public function resumenDia(){
        return $this->hasMany("app\Models\ResumenDia");
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
