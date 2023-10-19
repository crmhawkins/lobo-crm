<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;

    protected $table = "programa";

    protected $fillable = [
        'dia',
        "id_servicioEvento",
        "id_monitor",
        "comienzoMontaje",
        "comienzoEvento",
        "horas",
        "tiempoDesmontaje",
        "precioMonitor",
        "costoDesplazamiento",
        "pagado",
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class, "id_monitor", "id");
    }


    public function servicioEvento()
    {
        return $this->belongsTo("app\Models\ServicioEvento");
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