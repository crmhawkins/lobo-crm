<?php

namespace App\Http\Livewire\Gastos;

use App\Models\Alumno;
use App\Models\Cursos;
use App\Models\Empresa;
use App\Models\Presupuesto;
use App\Models\TipoGasto;
use App\Models\Gastos;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $tipos_gasto;
    public $nombre_gasto;
    public $date;
    public $cuantia;

    public function mount()
    {
        $this->tipos_gasto = TipoGasto::all();
    }

    public function render()
    {
        return view('livewire.gastos.create-component');
    }


    // Al hacer submit en el formulario
    public function submit()
    {

        // Validación de datos
        $validatedData = $this->validate(
            [
                'tipo_gasto' => 'required',
                'nombre_gasto' => 'required',
                'date' => 'nullable',
                'cuantia' => 'required',
            ],
            // Mensajes de error
            [
                'tipo_gasto.required' => 'Indique un tipo de gasto.',
                'nombre_gasto.required' => 'Ingrese un concepto de gasto',
                'cuantia.required' => 'Indique la cuantía del gasto',
            ]
        );

        // Guardar datos validados
        $GastosSave = Gastos::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 44, $GastosSave->id));

        // Alertas de guardado exitoso
        if ($GastosSave) {
            $this->alert('success', '¡Gasto registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del gasto!', [
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
            'submit',
            'destroy',
            'listarPresupuesto',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('gastos.index');
    }

    public function hydrate()
    {
        $this->emit('select2');
    }
}
