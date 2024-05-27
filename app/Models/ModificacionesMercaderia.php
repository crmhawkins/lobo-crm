<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModificacionesMercaderia extends Model
{
    use HasFactory;


    protected $table = 'modificaciones_mercaderia';

    protected $fillable = [
        'stock_mercaderia_entrante_id',
        'fecha',
        'motivo',
        'cantidad',
        'user_id',
        'tipo',
    ];

    public function stock_mercaderia_entrante()
    {
        return $this->belongsTo(StockMercaderiaEntrante::class);
    }
    
}
