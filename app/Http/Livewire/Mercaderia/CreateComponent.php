<?php

namespace App\Http\Livewire\Mercaderia;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Mercaderia;
use App\Models\MercaderiaCategoria;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{
    use LivewireAlert;

    public $nombre;
    public $precio;
    public $categorias;
    public $categoria_id;


    public function mount()
    {
        $this->categorias = MercaderiaCategoria::all();
        $this->categoria_id = 1;
    }

    public function render()
    {
        return view('livewire.mercaderia.create-component');
    }
    public function submit()
    {
        if (!is_numeric($this->categoria_id)) {
            $nueva_categoria = MercaderiaCategoria::create(['nombre' => $this->categoria_id]);
            $this->categoria_id = $nueva_categoria->id;
        }
        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
                'precio' => 'nullable',
                'categoria_id' => 'required',

            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'categoria_id.required' => 'La categoría es obligatoria.',
            ]
        );

        // Guardar datos validados
        $usuariosSave = Mercaderia::create($validatedData);

        // Alertas de guardado exitoso
        if ($usuariosSave) {
            $this->alert('success', '¡Mercadería registrada correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la mercadería!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('mercaderia.index');
    }
}
