<?php

namespace App\Http\Livewire\Stock;

use App\Models\Almacen;
use App\Models\Productos;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock;
use App\Models\ProductosCategories;
use App\Models\StockEntrante;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class EditComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $identificador;
    public $qr_id;
    public $numero;
    public $precio = 0;
    public $estado;
    public $fecha;
    public $observaciones;
    public $producto_seleccionado;
    public $unidades_producto;
    public $almacenes;
    public $almacen_id;
    public $productos_pedido = [];
    public $productos_disponibles = [];
    public $productos;
    public function mount()
    {
        $stock = Stock::where('qr_id', $this->identificador)->first();
        $this->fecha = $stock->fecha;
        $this->estado = $stock->estado;
        $this->qr_id = $this->identificador;
        $this->productos = Productos::all();
        $this->almacenes = Almacen::all();
        $user = Auth::user();
        $this->almacen_id = $stock->almacen_id;
        $stock_disponible = StockEntrante::where('stock_id', $stock->id)->get();
        foreach ($stock_disponible as $productoIndex => $producto) {
            $this->productos_disponibles[] = ['producto_id' => $producto->producto_id, 'lote_id' => $producto->lote_id, 'cantidad' => $producto->cantidad];
        }
    }

    public function render()
    {
        return view('livewire.stock.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {

        // Validación de datos
        $validatedData = $this->validate(
            [
                'lote_id' => 'required',
                'producto_id' => 'required',
                'cantidad_actual' => 'required',
                'cantidad_inicial' => 'required',
                'fecha_entrada' => 'required',
                'estado' => 'required',
            ],
            // Mensajes de error
            [
                'lote_id.required' => 'La identificación del lote es obligatoria.',
                'producto_id.required' => 'El ID de producto es obligatorio.',
                'cantidad_actual.required' => 'La cantidad de unidades del producto es obligatoria.',
                'cantidad_inicial.required' => 'La cantidad de unidades del producto es obligatoria.',
                'fecha_entrada.required' => 'La fecha de entrada del lote es obligatorio.',
                'estado.required' => 'El estado del lote es obligatoria.',
            ]
        );

        $productSave = $this->lote->update($validatedData);


        if ($productSave) {
            $this->alert('success', '¡Producto actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del producto!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Product updated successfully.');

        $this->emit('productUpdated');
    }

    // Elimina el producto
    public function destroy()
    {
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar el producto? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete',
            'update'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('stock.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $product = Productos::find($this->identificador);
        $product->delete();
        return redirect()->route('stock.index');
    }
    public function getProductoNombre()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->nombre != null) {
            return $producto->nombre;
        }
    }

    public function getProductoImagen()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->foto_ruta != null) {
            return $producto->foto_ruta;
        }
    }

    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }

    public function deleteArticulo($id)
    {
        unset($this->productos_pedido[$id]);
        $this->productos_pedido = array_values($this->productos_pedido);
    }

    public function addProducto($id)
    {
        $producto_existe = false;
        $producto_id = $id;
        foreach ($this->productos_pedido as $productos) {
            if ($productos['producto_id'] == $id) {
                $producto_existe = true;
                $producto_id = $productos['producto_id'];
            }
        }
        if ($producto_existe == true) {
            $producto = array_search($producto_id, array_column($this->productos_pedido, 'producto_id'));
            $this->productos_pedido[$producto]['cantidad'] = $this->productos_pedido[$producto]['cantidad'] + $this->unidades_producto;
        } else {
            $this->productos_pedido[] = ['producto_id' => $id, "cantidad" => $this->unidades_producto];
        }
        $this->producto_seleccionado = 0;
        $this->unidades_producto = 0;
        $this->emit('refreshComponent');
    }
}
