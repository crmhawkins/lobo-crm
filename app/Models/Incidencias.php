<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidencias extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'observaciones',
        'estado',
        'user_id',
        'notas',  // Añadir este campo

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
