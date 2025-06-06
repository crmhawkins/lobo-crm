<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mensaje extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'whatsapp_mensaje';

    protected $fillable = [
        'id_mensaje',
        'remitente',
        'mensaje',
        'status',
    ];




}
