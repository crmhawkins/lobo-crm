<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subalmacenes extends Model
{
    use HasFactory;

    protected $table = "subalmacenes";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'almacen_id',
        'almacen',
        'direccion',
        'horario',
    ];
}
