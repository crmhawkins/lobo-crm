<?php

namespace App\Http\Livewire\Stock;

use App\Models\Productos;
use App\Models\ProductoLote;
use App\Models\ProductosCategories;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class EditComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $identificador;
    public $producto;
    public $lote;
    public $nombre;
    public $cantidad_inicial;
    public $cantidad_actual;
    public $lote_id;
    public $producto_id;
    public $fecha_entrada;
    public $estado;

    public function mount()
    {
        $this->lote = ProductoLote::where("id", $this->identificador)->first();
        $this->producto = Productos::where('id', $this->lote->producto_id)->first();
        $this->nombre = $this->producto->nombre;
        $this->cantidad_inicial = $this->lote->cantidad_inicial;
        $this->cantidad_actual = $this->lote->cantidad_actual;
        $this->lote_id = $this->lote->lote_id;
        $this->producto_id = $this->lote->producto_id;
        $this->fecha_entrada = Carbon::parse($this->lote->fecha_entrada)->format('Y-m-d');
        $this->estado = $this->lote->estado;
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

}
