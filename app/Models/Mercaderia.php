<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mercaderia extends Model
{
    use HasFactory;

    protected $table = "mercaderia";

    protected $fillable = [
        'nombre',
        'categoria_id',
        'precio',
        'stock_seguridad'
    ];


    public function stockMercaderiaEntrante()
    {
        return $this->hasMany(StockMercaderiaEntrante::class);
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
