<?php

namespace App\Http\Livewire\Articulos;

use App\Models\Articulos;
use App\Models\ServicioCategoria;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{
    use LivewireAlert;

    public $name;
    public $stock;
    public $id_categoria;
    public $servicioCategorias;

    public function mount()
    {
        $this->servicioCategorias = ServicioCategoria::all();
    }

    public function render()
    {
        return view('livewire.articulos.create-component');
    }
   
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate(
            [
                'name' => 'required',
                'stock' => 'required',
                'id_categoria' => 'required',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre del articulo es obligatorio.',
                'stock.required' => 'El stock del articulo es obligatorio.',
                'id_categoria.required' => 'El stock del articulo es obligatorio.',
            ]
        );

        $departamentoExiste = Articulos::where('name', $validatedData)->get();

        if (count($departamentoExiste) > 0) {
            return $this->alert('error', '¡Ese nombre para el articulo ya existe!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        $departamentoSave = Articulos::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 23, $departamentoSave->id));

        // Alertas de guardado exitoso
        if ($departamentoSave) {
            $this->alert('success', 'Articulo registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del Articulo!', [
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
         return redirect()->route('articulos.index');
     }
}
