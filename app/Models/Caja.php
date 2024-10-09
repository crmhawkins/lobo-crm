<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = "caja";

    protected $fillable = [
        'fecha',
        'pedido_id',
        'poveedor_id',
        'descripcion',
        'importe',
        'metodo_pago',
        'tipo_movimiento',
        'banco',
        'estado',
        'nFactura',
        'nInterno',
        'iva',
        'descuento',
        'retencion',
        'fechaVencimiento',
        'fechaPago',
        'departamento',
        'delegacion_id',
        'cuenta',
        'documento_pdf',
        'importeIva',
        'total',
        'pagado',
        'pendiente',
        'asientoContable',
        'cuentaContable_id',
        'isIngresoProveedor',
        'gasto_id',
    ];

     // Relación con Proveedores (Una caja pertenece a un proveedor)
     public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'poveedor_id', 'id'); // Cambiado a 'poveedor_id'
    }

      // Relación con Facturas (Una caja puede tener muchas facturas asociadas)
    public function facturas()
    {
        return $this->hasMany(Facturas::class, 'id', 'pedido_id');
    }

    // Scope para filtrar solo las cajas que son ingresos
    public function scopeIngresos($query)
    {
        return $query->where('tipo_movimiento', 'ingreso');
    }

    // Scope para filtrar solo las cajas que son gastos
    public function scopeGastos($query)
    {
        return $query->where('tipo_movimiento', 'gasto');
    }

    public function gasto()
    {
        return $this->belongsTo(Caja::class, 'gasto_id');
    }

    public function delegacion()
    {
        return $this->belongsTo(Delegacion::class, 'delegacion_id');
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
