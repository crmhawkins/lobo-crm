<?php

namespace App\Http\Livewire\StockSubalmacen;

use App\Models\ProductosMarketing;
use App\Models\Subalmacenes;
use App\Models\StockSubalmacen;
use Livewire\Component;

class IndexComponent extends Component
{
    public $almacenes;
    public $selectedSubalmacen;
    public $productos = [];
    public $cantidad = [];

    public function mount()
    {
        // Cargar todos los subalmacenes
        $this->almacenes = Subalmacenes::all();

        // Seleccionar por defecto el primer subalmacén si existe
        if ($this->almacenes->count() > 0) {
            $this->selectedSubalmacen = $this->almacenes->first()->id;
        }

        // Cargar productos
        $this->loadProductos();
    }

    public function loadProductos()
    {
        // Cargar todos los productos de marketing
        $this->productos = ProductosMarketing::with(['stockSubalmacen' => function ($query) {
            $query->where('subalmacen_id', $this->selectedSubalmacen);
        }])->get();
    }

    public function updatedSelectedSubalmacen()
    {
        // Recargar productos cuando se cambie de subalmacén
        $this->loadProductos();
    }

    public function añadirStock($almacenId, $productoId)
    {
        $cantidad = $this->cantidad[$almacenId][$productoId] ?? 0;

        // Validar que la cantidad sea un entero positivo
        if (filter_var($cantidad, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) === false) {
            session()->flash('error', 'La cantidad debe ser un número entero positivo.');
            return;
        }

        // Crear un nuevo registro de entrada de stock
        StockSubalmacen::create([
            'subalmacen_id' => $almacenId,
            'producto_id' => $productoId,
            'cantidad' => $cantidad,
            'fecha' => now(),
            'tipo_entrada' => 'Añadir stock',
        ]);

        // Recargar productos después de la operación
        $this->loadProductos();
        $this->cantidad[$almacenId][$productoId] = null;
    }

    public function reducirStock($almacenId, $productoId)
    {
        $cantidad = $this->cantidad[$almacenId][$productoId] ?? 0;

        // Validar que la cantidad sea un entero positivo
        if (filter_var($cantidad, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) === false) {
            session()->flash('error', 'La cantidad debe ser un número entero positivo.');
            return;
        }

        // Calcular el stock actual antes de la salida
        $stockActual = StockSubalmacen::where('subalmacen_id', $almacenId)
            ->where('producto_id', $productoId)
            ->sum('cantidad');

        // Si no hay suficiente stock, no permitir la reducción
        if ($stockActual < $cantidad) {
            session()->flash('error', 'No hay suficiente stock para reducir esa cantidad.');
            return;
        }

        // Crear un nuevo registro de salida de stock
        StockSubalmacen::create([
            'subalmacen_id' => $almacenId,
            'producto_id' => $productoId,
            'cantidad' => $cantidad,
            'fecha' => now(),
            'tipo_salida' => 'Reducir stock',
        ]);

        // Recargar productos después de la operación
        $this->loadProductos();
        $this->cantidad[$almacenId][$productoId] = null;
    }

    public function render()
{
    $almacenSeleccionado = Subalmacenes::find($this->selectedSubalmacen);

    return view('livewire.stock-subalmacen.index-component', [
        'almacenSeleccionado' => $almacenSeleccionado,
        'productos' => $this->productos->map(function($producto) use ($almacenSeleccionado) {
            $producto->stock_actual = StockSubalmacen::stockActual($almacenSeleccionado->id, $producto->id);
            return $producto;
        }),
    ]);
}
}
