<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockSaliente extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'stock_salientes';

    protected $fillable = [
        'stock_entrante_id',
        'producto_id',
        'cantidad_salida',
        'fecha_salida',
        'motivo_salida',
    ];

    public function stockEntrante()
    {
        return $this->belongsTo(StockEntrante::class, 'stock_entrante_id');
    }
}
