<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidosDocuments extends Model
{
    use HasFactory;

    protected $table = 'pedidos_documents';

    protected $fillable = [
        'pedido_id',
        'path',
        'original_name'
    ];
}
