<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductosMarketing extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = "productos_marketing";

    protected $fillable = [
        'nombre',
        'description',
        'materiales',
        'peso_neto_unidad',
        'unidades_por_caja',
        'cajas_por_pallet',
        'foto_ruta',
    ];

    public function subalmacenes()
{
    return $this->belongsToMany(Subalmacenes::class, 'stock_subalmacen', 'producto_id', 'subalmacen_id')
                ->withPivot('cantidad');
}

public function stockEnAlmacen($almacenId)
    {
        $tockEntrante = StockSubalmacen::where('subalmacen_id', $almacenId)
            ->where('producto_id', $this->id)
            ->where('tipo_entrada', '!=', null)
            ->where('tipo_salida', '=', null)
            ->sum('cantidad');

        $stockSalida = StockSubalmacen::where('subalmacen_id', $almacenId)
            ->where('producto_id', $this->id)
            ->where('tipo_entrada', '=', null)
            ->where('tipo_salida', '!=', null)
            ->sum('cantidad');
        
        return $tockEntrante - $stockSalida;

    }

    public function stockSubalmacen()
    {
        return $this->hasMany(StockSubalmacen::class, 'producto_id', 'id');
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
