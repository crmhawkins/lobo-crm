<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "stock";

    protected $fillable = [
        'qr_id',
        'estado',
        'fecha',
        'observaciones',
        'almacen_id'
        ];


    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function entrantes()
    {
        return $this->hasOne(StockEntrante::class, 'stock_id');
    }

}
