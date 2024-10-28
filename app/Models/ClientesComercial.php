<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;




class ClientesComercial extends Model
{
    use HasFactory;
    use softDeletes;


    protected $table = "clientes_comercial";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'comercial_id',
        'nombre',
        'cif',
        'direccion',
        'provincia',
        'localidad',
        'cod_postal',
        'telefono',
        'email',
        'distribuidor_id',
    ];


    public function comercial()
    {
        return $this->belongsTo(User::class, 'comercial_id');
    }

    public function distribuidor()
    {
        return $this->belongsTo(Clients::class, 'distribuidor_id');
    }


}
