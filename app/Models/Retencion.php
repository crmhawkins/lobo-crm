<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retencion extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'retencion';

    protected $fillable = ['nombre', 'porcentaje', 'dias_retencion'];

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}
