<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockEntrante extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "stock_entrante";

    protected $with = ['salidas'];


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
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function salidas()
    {
        return $this->hasMany(StockSaliente::class, 'stock_entrante_id');
    }

    public function isEmpty()
    {

        return $this->salidas()->count() === 0;
    }
}
