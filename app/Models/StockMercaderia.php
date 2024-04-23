<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMercaderia extends Model
{
    use HasFactory;
    use SoftDeletes;

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
