<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = "servicios";

    protected $fillable = [
        'nombre',
        'id_categoria',
        'id_pack',
        'tiempoMontaje',
        'tiempoDesmontaje',
        'tiempoServicio',
        'precioBase',
        'precioMonitor',
        'precioMonitorNocturno',
        'precioMonitorAnimacion',
        'minMonitor'

    ];

    public function programas()
    {
        return $this->hasMany('app\Models\Programa');
    }

    public function articulos()
    {
        return $this->belongsToMany('app\Models\Articulos', 'servicio_articulo', 'servicio_id', 'articulo_id')->withPivot('stock_usado');
    }

    public function pack(){
        return $this->belongsTo("app\Models\ServicioPack");
    }

    public function categoria(){
       return $this->belongsTo("app\Models\ServicioCategoria");
    }

    public function presupuestos()
    {
        return $this->belongsToMany(Presupuesto::class, 'servicio_presupuesto', 'servicio_id', 'presupuesto_id')->withPivot('numero_monitores', 'precioFinal', 'tiempo', 'tiempo_montaje', 'tiempo_desmontaje', 'hora_montaje', 'hora_inicio', 'hora_finalizacion', 'id_monitores', 'sueldo_monitores', 'gasto_gasoil', 'pago_pendiente');
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
