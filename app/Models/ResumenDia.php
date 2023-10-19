<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumenDia extends Model
{
    use HasFactory;

    protected $table = "resumen_dia";

    protected $fillable = [
        "dia",
        'id_programa',
        "id_evento",
    ];

    public function programa()
    {
        return $this->belongsTo("app\Models\Programa");
    }

    public function evento()
    {
        return $this->belongsTo("app\Models\Evento");
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