<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoEmails extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'tipo_emails';
    protected $fillable = ['nombre'];


}
