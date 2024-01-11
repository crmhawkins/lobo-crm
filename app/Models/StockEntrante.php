<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntrante extends Model
{
    use HasFactory;

    protected $table = "stock_entrante";

    protected $fillable = [
        'stock_id',
        'lote_id',
        'orden_numero',
        'producto_id',
        'cantidad',
        ];


    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
}
