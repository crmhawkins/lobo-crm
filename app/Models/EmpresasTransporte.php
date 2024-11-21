<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresasTransporte extends Model
{
    use HasFactory;

    protected $table = 'empresas_transporte';

    protected $fillable = ['nombre'];


   
}
