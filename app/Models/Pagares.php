<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Bancos;
class Pagares extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['caja_id', 'nPagos', 'fecha_efecto', 'nEfecto', 'banco_id', 'estado', 'importe_efecto'];



    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }



    public function banco()
    {
        return $this->belongsTo(Bancos::class);
    }
}
