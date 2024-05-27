<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoturaMercaderia extends Model
{
    use HasFactory;

    protected $table = 'rotura_mercaderia';

    protected $fillable = [
        'stock_mercaderia_entrante_id',
        'fecha',
        'motivo',
        'cantidad',
        'user_id'
    ];

    public function stock_mercaderia_entrante()
    {
        return $this->belongsTo(StockMercaderiaEntrante::class);
    }


        
}
