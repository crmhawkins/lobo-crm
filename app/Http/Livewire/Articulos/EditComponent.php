<?php

namespace App\Http\Livewire\Articulos;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Articulos;
use App\Models\ServicioCategoria;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $name;
    public $stock;
    public $id_categoria;
    public $servicioCategorias;

    public function mount()
    {
        $this->servicioCategorias = ServicioCategoria::all();
        $articulo = Articulos::find($this->identificador);
        $this->name = $articulo->name;
        $this->stock = $articulo->stock;
        $this->id_categoria = $articulo->id_categoria;
    }
    public function render()
    {
        return view('livewire.articulos.edit-component');
    }
    public function update()
    {
        // Validación de datos
        $this->validate(
            [
                'name' => 'required',
                'stock' => 'required',
                'id_categoria' => 'required',

            ],
            // Mensajes de error
            [
                'name.required' => 'El nombre es obligatorio.',
                'stock.required' => 'El stock es obligatorio.',

            ]
        );

        // Encuentra el identificador
        $articulo = Articulos::find($this->identificador);

        // Guardar datos validados
        $eventoSave = $articulo->update([
            'name' => $this->name,
            'stock' => $this->stock,
            'id_categoria' => $this->id_categoria,

        ]);
        event(new \App\Events\LogEvent(Auth::user(), 24, $articulo->id));

        if ($eventoSave) {
            $this->alert('success', '¡Artículo actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del articulo!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Evento actualizado correctamente.');

    }
    // Eliminación
    public function destroy()
    {

        $this->alert('warning', '¿Seguro que desea borrar el artículo? No hay vuelta atrás', [
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
        return redirect()->route('articulos.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $articulo = Articulos::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 25, $articulo->id));
        $evento = Evento::find($this->identificador);
        $evento->delete();
        return redirect()->route('articulos.index');
    }
}
