<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioCategoria extends Model
{
    use HasFactory;

    protected $table = "servicio_categorias";

    protected $fillable = [
        'nombre',
    ];

    public function servicios() {
       return $this->hasMany("app\Models\Servicio", "id_categoria", "id");
    }

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}