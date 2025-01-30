<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturasCompensadas extends Model
{
    use HasFactory;

    protected $table = "facturas_compensadas";

    protected $fillable = [
        'caja_id',
        'factura_id',
        'importe',
        'pagado',
        'pendiente',
        'fecha',
    ];

    public function factura()
    {
        return $this->belongsTo(Facturas::class, 'factura_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
