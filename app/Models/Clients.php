<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    use HasFactory;
    protected $table = "clientes";

    protected $fillable = [
        'nombre',
        'dni_cif',
        'direccion',
        'provincia',
        'localidad',
        'cod_postal',
        'telefono',
        'email',
        'tipo_cliente',
        'forma_pago_pref',
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
