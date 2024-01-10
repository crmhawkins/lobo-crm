<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    use HasFactory;

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
        "telefono",
        "email",
        'tipo_cliente',
        "forma_pago_pref",
        "estado",
        "precio_crema",
        "precio_vodka07l",
        "precio_vodka175l",
        "precio_vodka3l",
        "nota",
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
