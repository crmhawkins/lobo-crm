<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class GiroBancario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'giro_bancario';

    protected $fillable = ['factura_id', 'banco_id', 'fecha_programacion', 'estado'];

    public function factura()
    {
        return $this->belongsTo(Facturas::class, 'factura_id');
    }

    public function banco()
    {
        return $this->belongsTo(Bancos::class);
    }
}
