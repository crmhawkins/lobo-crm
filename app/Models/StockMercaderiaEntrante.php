<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMercaderiaEntrante extends Model
{
    use HasFactory;
    use SoftDeletes;

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
