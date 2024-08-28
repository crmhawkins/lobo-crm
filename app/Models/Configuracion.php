<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuracion';

    protected $fillable = [
        'cuenta',
        'firma',
        'texto_factura',
        'texto_pedido',
        'texto_albaran',
        'texto_email'
    ];

    public $timestamps = false;
}
