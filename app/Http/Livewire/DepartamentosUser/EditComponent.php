<?php

namespace App\Http\Livewire\DepartamentosUser;

use App\Models\DepartamentosUser;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $departamento;
    public $name;

    public function mount()
    {
        $this->departamento = DepartamentosUser::find($this->identificador);
        // dd($this->departamento);
        if ($this->departamento) {
            $this->name = $this->departamento->name;
        }
    }
    
    public function render()
    {
        return view('livewire.departamentos-user.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'name' => 'required',
 
        ],
            // Mensajes de error
            [
                'name.required' => 'El nombre del departamento es obligatorio.',
            
            ]);

        // Encuentra el identificador
        $departamentoFind = DepartamentosUser::find($this->identificador);

        // Guardar datos validados
        $departamentoSave = $departamentoFind->update([
            'name' => $this->name,
            
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 42, $departamentoFind->id));

        if ($departamentoSave) {
            $this->alert('success', 'Departamento actualizado correctamente!', [
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

        session()->flash('message', 'departamento actualizado correctamente.');

        $this->emit('eventUpdated');
    }
      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el usuario? No hay vuelta atrás', [
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
             'update'
         ];
     }
 
     // Función para cuando se llama a la alerta
     public function confirmed()
     {
         // Do something
         return redirect()->route('departamentos.index');
     }
     // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $departamento = DepartamentosUser::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 43, $departamento->id));
        $departamento->delete();
        return redirect()->route('departamentos.index');

    }
}

