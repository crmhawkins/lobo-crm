<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alertas extends Model
{
    use HasFactory;

    protected $table = "alertas";
    protected $fillable = [
        'user_id',
        'status_id',
        'stage',
        'cont_postpone',
        'datetime',
        'referencia_id',
        'descripcion',
        'observaciones',
        
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    
    
    
    
    
    
    
    
}
