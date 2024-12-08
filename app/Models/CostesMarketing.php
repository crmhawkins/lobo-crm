<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostesMarketing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'costes_marketing';

    protected $fillable = [
        'product_id',
        'cost',
        'COD',
        'year',
    ];

    public function producto()
    {
        return $this->belongsTo(ProductosMarketing::class, 'product_id');
    }

    public function delegacion()
    {
        return $this->belongsTo(Delegacion::class, 'COD', 'COD');
    }
}
