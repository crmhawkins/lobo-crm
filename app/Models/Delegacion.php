<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delegacion extends Model
{
    use HasFactory;

    protected $table = "delegaciones";

    protected $fillable = [
        'COD',
        'nombre',
        ];


    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function clientes()
    {
        return $this->hasMany(Clients::class, 'delegacion_COD', 'COD');
    }

    public function Proveedores()
    {
        return $this->hasMany(Proveedores::class, 'delegacion_COD', 'COD');

    }
}
