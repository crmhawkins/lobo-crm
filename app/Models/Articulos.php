<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulos extends Model
{
    use HasFactory;

    protected $table = "articulos";

    protected $fillable = [
        "name",
        "stock",
        "id_categoria",

    ];

    public function servicios()
    {
        return $this->belongsToMany('app\Models\Servicio', 'servicio_articulo', 'articulo_id', 'servicio_id')->withPivot('stock_usado');
    }

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
