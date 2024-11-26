<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AcuerdosComerciales;

class DocumentosAcuerdosComerciales extends Model
{
    use HasFactory;

    protected $table = 'documentos_acuerdos_comerciales';

    protected $fillable = [
        'acuerdo_comercial_id',
        'ruta'
    ];

    public function acuerdoComercial()
    {
        return $this->belongsTo(AcuerdosComerciales::class);
    }
}
