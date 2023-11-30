<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMercaderia extends Model
{
    use HasFactory;

    protected $table = "stock_mercaderia";

    protected $fillable = [
        'qr_id',
        'lote_id',
        'estado',
        'fecha',
        'observaciones',
        ];


    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function entrantes()
    {
        return $this->hasMany(StockMercaderiaEntrante::class, 'stock_id');
    }
}
