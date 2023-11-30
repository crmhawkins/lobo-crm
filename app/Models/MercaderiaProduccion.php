<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MercaderiaProduccion extends Model
{
    use HasFactory;

    protected $table = "mercaderia_produccion";

    protected $fillable = [
        'orden_id',
        'mercaderia_id',
        'cantidad',
    ];


    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
