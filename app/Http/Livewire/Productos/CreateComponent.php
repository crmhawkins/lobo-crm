<?php

namespace App\Http\Livewire\Productos;

use App\Models\Productos;
use App\Models\ProductosCategories;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateComponent extends Component
{

    use LivewireAlert;
    use WithFileUploads;

    public $nombre;
    public $precio;
    public $iva;
    public $foto_ruta;
    public $unidades_disponibles = 0;
    public $unidades_reservadas = 0;
    public $unidades_vendidas = 0;

    public function mount()
    {
    }

    public function render()
    {
        return view('livewire.productos.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {

        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
                'precio' => 'required',
                'iva' => 'required',
                'foto_ruta' => 'required',
                'unidades_disponibles' => 'required',
                'unidades_reservadas' => 'required',
                'unidades_vendidas' => 'required',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'La Categoria es obligatoria.',
                'precio.required' => 'El código de producto es obligatorio.',
                'iva.required' => 'La descripción es obligatoria.',
                'foto_ruta.required' => 'El precio es obligatorio.',
                'unidades_disponibles.required' => 'El nombre es obligatorio.',
                'unidades_reservadas.required' => 'La descripción es obligatoria.',
                'unidades_vendidas.required' => 'El precio es obligatorio.',
            ]
        );
        $name = md5($this->foto_ruta . microtime()).'.'.$this->foto_ruta->extension();

        $this->foto_ruta->storePubliclyAs('public', 'photos/' . $name);

        $validatedData['foto_ruta'] = $name;

        // Guardar datos validados
        $productosSave = Productos::create($validatedData);

        // Alertas de guardado exitoso
        if ($productosSave) {
            $this->alert('success', '¡Producto registrado correctamente!', [
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
        return redirect()->route('productos.index');
    }
}
