<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnotacionesClientePedido extends Model
{

    use HasFactory;

    protected $table = 'anotaciones_cliente_pedido';

    protected $fillable = [
        'cliente_id',
        'anotacion',
        'estado'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }




}
