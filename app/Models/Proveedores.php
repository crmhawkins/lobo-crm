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
        "delegacion_COD",
        "cuenta",
        "forma_pago_pref",
        "departamento_id",
        'cuenta_contable_numero'
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function delegacion()
    {
        return $this->belongsTo(Delegacion::class, 'delegacion_COD', 'COD');
    }


    //has many cajas

     // Relación con Caja (Un proveedor tiene muchas cajas)
     public function cajas()
     {
         return $this->hasMany(Caja::class, 'proveedor_id', 'id');
     }


      // Relación con SubCuentaHijo (cuenta contable)
    public function cuentaContable()
    {
        return $this->belongsTo(SubCuentaHijo::class, 'cuenta_contable', 'numero');
    }
}
