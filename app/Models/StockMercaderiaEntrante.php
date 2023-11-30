<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMercaderiaEntrante extends Model
{
    use HasFactory;

    protected $table = "stock_mercaderia_entrante";

    protected $fillable = [
        'stock_id',
        'mercaderia_id',
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
}
