<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRegistro extends Model
{
    use HasFactory;

    protected $table = 'stock_registro';

    protected $fillable = [
        'stock_entrante_id',
        'cantidad',
        'tipo',
        'factura_id',
        'motivo',
        'pedido_id',
    ];

    public function stockEntrante()
    {
        return $this->belongsTo(StockEntrante::class, 'stock_entrante_id');
    }
}
