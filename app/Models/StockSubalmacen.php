<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSubalmacen extends Model
{
    use HasFactory;

    protected $table = "stock_subalmacen";

    protected $fillable = [
        'subalmacen_id',
        'producto_id',
        'cantidad',
        'fecha',
        'observaciones',
        'tipo_entrada',
        'tipo_salida',
    ];

    public function subalmacen()
    {
        return $this->belongsTo(Subalmacenes::class, 'subalmacen_id');
    }

    public function producto()
    {
        return $this->belongsTo(ProductosMarketing::class, 'producto_id');
    }

    // Método para obtener todas las entradas
    public function scopeEntradas($query)
    {
        return $query->whereNotNull('tipo_entrada')->whereNull('tipo_salida');
    }

    // Método para obtener todas las salidas
    public function scopeSalidas($query)
    {
        return $query->whereNull('tipo_entrada')->whereNotNull('tipo_salida');
    }

    // Calcular el stock actual para un producto en un subalmacén específico
    public static function stockActual($subalmacenId, $productoId)
    {
        // Calcular entradas
        $entradas = self::where('subalmacen_id', $subalmacenId)
            ->where('producto_id', $productoId)
            ->entradas()
            ->sum('cantidad');

        // Calcular salidas
        $salidas = self::where('subalmacen_id', $subalmacenId)
            ->where('producto_id', $productoId)
            ->salidas()
            ->sum('cantidad');

        // Retornar el stock actual
        return $entradas - $salidas;
    }
}
