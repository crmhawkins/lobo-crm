<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Costes extends Model
{
    use HasFactory;

    protected $table = "costes";
    protected $fillable = [
        'id',
        'product_id',
        'cost',
        'year',
        'COD',

    ];


    public function producto()
    {
        return $this->belongsTo(Productos::class, 'product_id');
    }

    public function delegacion()
    {
        return $this->belongsTo(Delegacion::class, 'COD', 'COD');
    }
    
}
