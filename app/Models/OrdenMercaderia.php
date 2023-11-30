<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenMercaderia extends Model
{
    use HasFactory;

    protected $table = "orden_mercaderia";

    protected $fillable = [
        'precio',
        'numero',
        'estado',
        'fecha',
        'observaciones',
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
