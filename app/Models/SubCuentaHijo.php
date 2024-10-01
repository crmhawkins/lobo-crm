<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\GrupoContable;
use App\Models\SubCuentaContable;
use App\Models\CuentasContable;
use App\Models\SubGrupoContable;

class SubCuentaHijo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sub_cuenta_hija';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'sub_cuenta_id',
        'numero',
        'nombre',
        'descripcion'
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at', 
    ];

    /**
     * Obtener el Grupo
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cuenta()
    {
        return $this->belongsTo(SubCuentaContable::class,'sub_cuenta_id');
    }

    public function subCuenta()
{
    return $this->belongsTo(SubCuentaContable::class, 'sub_cuenta_id');
}

    public function grupoContable()
    {
        // Verificar que la relación 'cuenta' existe antes de acceder a ella
        if ($this->cuenta && $this->cuenta->cuenta && $this->cuenta->cuenta->grupo && $this->cuenta->cuenta->grupo->grupo) {
            return $this->cuenta->cuenta->grupo->grupo;
        }
    
        // Retornar null si no hay relación
        return null;
    }
}
