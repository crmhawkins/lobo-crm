<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroEmail extends Model
{
    use HasFactory;

    protected $table = 'registro_email';

    protected $fillable = [
        'factura_id',
        'pedido_id',
        'cliente_id',
        'email',
        'user_id',
        'tipo_id',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
