<?php

namespace App\Http\Livewire\Productoscategories;

use App\Models\ProductosCategories;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $nombre;

    public function mount($identificador)
    {
        $product = ProductosCategories::find($identificador);

        $this->nombre = $product->nombre;
    }

    public function render()
    {
        return view('livewire.productos_categories.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'nombre' => 'required',
        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
            ]);

        // Encuentra el producto identificado
        $product = ProductosCategories::find($this->identificador);

        // Guardar datos validados
        $productSave = $product->update([
            'nombre' => $this->nombre,
        ]);

        if ($productSave) {
            $this->alert('success', 'Categoría actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la categoría!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Categoría editada correctamente.');

        $this->emit('productUpdated');
    }

      // Elimina el producto
      public function destroy(){


        $this->alert('warning', '¿Seguro que desea borrar la categoría? No hay vuelta atrás', [
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
            'confirmDelete'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('productos-categories.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $product = ProductosCategories::find($this->identificador);
        $product->delete();
        return redirect()->route('productos-categories.index');

    }
}
