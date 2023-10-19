<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contrato extends Model
{
    use HasFactory;

    protected $table = "contrato";

    protected $fillable = [
        'id_presupuesto',
        'metodoPago',
        'cuentaTransferencia',
        'dia',
        'observaciones',
        'authImagen',
        'authMenores',
        'ruta',
        'created_at',
        'updated_at',
        'deleted_at',

    ];


    public function presupuesto(): HasOne{
        return $this->hasOne(Presupuesto::class, "id_presupuesto", "id");
    }

    // public function servicios()
    // {
    //     return $this->hasMany('app\Models\ServicioEvento');
    // }
    
    // public function evento() : HasOne
    // {
    //     return $this->hasOne(Evento::class, "id_evento");
    // }

    public function metodoPago() : BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, "metodoPago", "id");
    }

    // public function resumenDia(){
    //     return $this->hasMany("app\Models\ResumenDia");
    // }
    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}