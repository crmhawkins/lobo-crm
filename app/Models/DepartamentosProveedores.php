<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartamentosProveedores extends Model
{
    use HasFactory;

    protected $table = 'departamentos_proveedores';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];
    
    public function proveedores()
    {
        return $this->hasMany(Proveedores::class, 'departamento_id');
    }
}
