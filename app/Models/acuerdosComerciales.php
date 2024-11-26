<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//soft deletes
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Clientes;
use App\Models\User;


class acuerdosComerciales extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'acuerdos_comerciales';

    protected $fillable = [
        'user_id',
        'cliente_id',
        'nAcuerdo',
        'nombre_empresa',
        'cif_empresa',
        'nombre',
        'dni',
        'email',
        'telefono',
        'domicilio',
        'establecimiento',
        'fecha_inicio',
        'fecha_fin',
        'prductos_lobo',
        'productos_otros',
        'marketing',
        'observaciones',
        'firma_comercial_lobo',
        'firma_comercial',
        'firma_cliente',
        'firma_distribuidor',
        'domicilio_establecimientos',
        'fecha_firma',
    ];

    //user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //cliente
    public function cliente()
    {
        return $this->belongsTo(Clientes::class);
    }

    //documentos
    public function documentos()
    {
        return $this->hasMany(DocumentosAcuerdosComerciales::class);
    }
}
