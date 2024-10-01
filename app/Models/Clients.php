<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clients extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "clientes";

    protected $attributes = [
        // Valores predeterminados
        'estado' => 1,
        "precio_crema"=> 8.34,
        "precio_vodka07l"=> 23.50,
        "precio_vodka175l"=> 52,
        "precio_vodka3l"=> 135,
    ];

    protected $fillable = [
        "nombre",
        "dni_cif",
        "direccion",
        "provincia",
        "localidad",
        "cod_postal",
        'direccionenvio',
        'provinciaenvio',
        'localidadenvio',
        'codPostalenvio',
        'usarDireccionEnvio',
        "telefono",
        "email",
        'tipo_cliente',
        "forma_pago_pref",
        "vencimiento_factura_pref",
        "estado",
        "porcentaje_bloq",
        "precio_crema",
        "precio_vodka07l",
        "precio_vodka175l",
        "precio_vodka3l",
        "nota",
        "cuenta_contable",
        "delegacion_COD",
        "comercial_id",
        "cuenta",
        "observaciones",
        "credito"
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
        return $this->belongsTo(Delegacion::class, 'delegacion_COD');
    }
    public function comercial()
    {
        return $this->belongsTo(User::class, 'comercial_id');
    }
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }
    // Relación con SubCuentaHijo (cuenta contable)
    public function cuentaContable()
    {
        return $this->belongsTo(SubCuentaHijo::class, 'cuenta_contable', 'numero');
    }
}
