<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostesProductos extends Model
{
    use HasFactory; 

    protected $fillable = [
        'producto_id',
        'coste',
        'fecha'
    ];

    public function productos()
    {
        return $this->belongsTo(Productos::class);
    }
}
