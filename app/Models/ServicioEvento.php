<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServicioEvento extends Model
{
    use HasFactory;

    protected $table = "servicio_evento";

    protected $fillable = [
        'id_evento',
        'id_servicio',
        'horaInicio',
        'tiempo',
        'dia',
        'numMonitores',
        'importe',
        'importeBase',
        'descuento',
        'comienzoMontaje',
        'tiempoMontaje',
        'tiempoDesmontaje',

    ];


    // public function programas()
    // {
    //     return $this->hasMany('app\Models\Programa');
    // }

    public function contrato(){
        return $this->belongsTo("app\Models\Contrato");
    }

    public function servicio(): HasOne{
        return $this->hasOne(Servicio::class, "id", "id_servicio");
    }


    // public function categoria(){
    //    return $this->belongsTo("app\Models\ServicioCategoria");
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