<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoturaStock extends Model
{
    use HasFactory;

    protected $table = 'rotura_stock';

    protected $fillable = [
        'stock_id',
        'cantidad',
        'fecha',
        'observaciones',
        'almacen_id',
        'user_id',
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
