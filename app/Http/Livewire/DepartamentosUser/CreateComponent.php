<?php

namespace App\Http\Livewire\DepartamentosUser;

use App\Models\DepartamentosUser;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

use Livewire\Component;

class CreateComponent extends Component
{
    use LivewireAlert;

    public $name;

    public function render()
    {
        return view('livewire.departamentos-user.create-component');
    }
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate(
            [
            'name' => 'required',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre del departamento es obligatorio.',
            ]
        );

        $departamentoExiste = DepartamentosUser::where('name', $validatedData)->get();
        if (count($departamentoExiste) > 0) {
            return $this->alert('error', '¡Ese nombre para el departamento ya existe!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        $departamentoSave = DepartamentosUser::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 41, $departamentoSave->id));

        // Alertas de guardado exitoso
        if ($departamentoSave) {
            $this->alert('success', 'Departamento registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del departamento!', [
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
         return redirect()->route('departamentos.index');
     }
}
