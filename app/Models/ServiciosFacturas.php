<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiciosFacturas extends Model
{
    use HasFactory;

    protected $table = 'servicios_facturas';

    protected $fillable = [
        'factura_id',
        'cantidad',
        'precio',
        'total',
        'descripcion'
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicios::class);
    }

    public function factura()
    {
        return $this->belongsTo(Facturas::class);
    }


    
}
