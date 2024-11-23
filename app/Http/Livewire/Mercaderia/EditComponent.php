<?php

namespace App\Http\Livewire\Mercaderia;


use App\Models\Mercaderia;
use App\Models\MercaderiaCategoria;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $nombre;
    public $precio;
    public $categoria_id;
    public $categorias;
    public $stock_seguridad;



    public function mount()
    {
        $mercaderia = Mercaderia::find($this->identificador);
        $this->nombre = $mercaderia->nombre;
        $this->categoria_id = $mercaderia->categoria_id;
        $this->categorias = MercaderiaCategoria::all();
        $this->stock_seguridad = $mercaderia->stock_seguridad;
        
    }
    public function render()
    {
        return view('livewire.mercaderia.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        if (!is_numeric($this->categoria_id)) {
            $nueva_categoria = MercaderiaCategoria::create(['nombre' => $this->categoria_id]);
            $this->categoria_id = $nueva_categoria->id;
        }
        // Validación de datos
        $this->validate(
            [
                'nombre' => 'required',
                'categoria_id' => 'required',
                'precio' => 'nullable',
                'stock_seguridad' => 'nullable',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'categoria_id.required' => 'La categoría es obligatoria.',
                'precio.required' => 'El precio es obligatorio.',
            ]
        );

        // Encuentra el identificador
        $mercaderia = Mercaderia::find($this->identificador);

        // Guardar datos validados
        $tipoSave = $mercaderia->update([
            'nombre' => $this->nombre,
            'categoria_id' => $this->categoria_id,
            'stock_seguridad' => $this->stock_seguridad,
        ]);

        if ($tipoSave) {
            $this->alert('success', '¡Mercadería actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la mercadería!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Mercadería actualizada correctamente.');

        $this->emit('eventUpdated');
    }

    // Eliminación
    public function destroy()
    {

        $this->alert('warning', '¿Seguro que desea borrar la mercadería? No hay vuelta atrás', [
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
        return redirect()->route('mercaderia.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $tipo_gasto = Mercaderia::find($this->identificador);
        $tipo_gasto->delete();
        return redirect()->route('mercaderia.index');
    }
}
