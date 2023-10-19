<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackPresupuesto extends Model
{
    use HasFactory;

    protected $table = "pack_presupuesto";

    protected $fillable = [
        'servicio_id',
        'presupuesto_id',
        'numero_monitores',
        'precio_final',
        'tiempo',
        'horas_inicio',
        'horas_finalizacion',
        'tiempos_montaje',
        'tiempos_desmontaje',
        'horas_montaje',
        'id_monitores',
        'sueldos_monitores',
        'gastos_gasoil',
        'pagos_pendientes',    ];

    public function servicios() {
       return $this->hasMany("App\Models\Servicio", "id_pack", "id");
    }

    public function presupuestos()
    {
        return $this->belongsToMany(Presupuesto::class, 'servicio_presupuesto', 'pack_id', 'presupuesto_id')->withPivot('numero_monitores', 'precioFinal', 'tiempos', 'tiempos_montaje', 'tiempos_desmontaje', 'horas_montaje','horas_inicio', 'horas_finalizacion', 'id_monitores', 'sueldos_monitores', 'gastos_gasoil', 'pagos_pendientes');
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
