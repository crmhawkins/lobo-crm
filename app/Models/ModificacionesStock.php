<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModificacionesStock extends Model
{
    use HasFactory;

    protected $table = 'modificaciones_stock';

    protected $fillable = [
        'stock_id',
        'user_id',
        'tipo',
        'cantidad',
        'motivo',
        'fecha',
        'almacen_id',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
