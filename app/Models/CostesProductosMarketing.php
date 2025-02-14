<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostesProductosMarketing extends Model
{
    use HasFactory;

    protected $table = 'costes_productos_marketing';

    protected $fillable = [
        'producto_id',
        'coste',
        'fecha',
    ];

    public function producto()
    {
        return $this->belongsTo(ProductosMarketing::class);
    }
}
