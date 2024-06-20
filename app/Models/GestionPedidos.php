<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionPedidos extends Model
{
    use HasFactory;

    protected $table = 'gestion_pedidos';


    protected $fillable = [
        'pedido_id',
        'estado',
        'gestion'
    ];

}
