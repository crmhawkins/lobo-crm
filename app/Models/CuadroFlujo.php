<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuadroFlujo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cuadro_flujo';

    protected $fillable = ['mes', 'anio', 'saldo_inicial', 'saldo_final', 'banco_id'];

    public static function getOrCreateForMonth($mes, $anio, $banco_id)
    {
        return self::firstOrCreate(
            ['mes' => $mes, 'anio' => $anio, 'banco_id' => $banco_id],
            ['saldo_inicial' => 0, 'saldo_final' => 0]
        );
    }
}
