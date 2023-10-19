<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = "cliente";

    protected $fillable = [
        "trato",
        "nombre",
        "apellido",
        "tipoCalle",
        "calle",
        "numero",
        "direccionAdicional3",
        "direccionAdicional1",
        "direccionAdicional2",
        "codigoPostal",
        "ciudad",
        "nif",
        "tlf3",
        "tlf1",
        "tlf2",
        "email1",
        "email2",
        "email3",
        "confPostal",
        "confEmail",
        "confSms",
        'tipoCliente',
        'codigo_organo_gestor',
        'codigo_unidad_tramitadora',
        'codigo_oficina_contable'
    ];

    public function eventos(){
        return $this->hasMany("app\Models\Evento");
    }

    public function contratos(){
        return $this->hasMany("app\Models\Contrato");
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