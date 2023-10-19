<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GastosMensuales extends Model
{
    use HasFactory;

    protected $table = "gastos_mensuales";

    protected $fillable = [
        'mes',
        'personalCentralYSegSoc',
        'alarma',
        'seguro',
        'telefonia',
        'gestoriaFiscal',
        'gestoriaLaboral',
        'alquileres',
        'bancos',
        'aberasConsultores',
        'informatico',
        'comunidad',
        'suministros',
        'digmep',
        'carlos',
        'mybox',
        'created_at',
        'updated_at',
        'deleted_at',

    ];
}
