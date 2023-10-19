<?php

namespace App\Http\Livewire\ServiciosPacks;

use App\Models\ServicioPack;
use App\Models\Servicio;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $nombre;

    public $servicios;
    public $servicio;
    public $serviciosPack = [];

    public $serviciosPackIDs = [];


    public function mount()
    {
        $this->servicios = Servicio::all();
    }

    public function removeServ($key)
    {
        unset($this->serviciosPack[$key]);
    }

    public function addServ()
    {
        if (!in_array($this->servicio, $this->serviciosPackIDs)) {
            $servicio = Servicio::where('id', $this->servicio)->first();
            $this->serviciosPack[count($this->serviciosPack)] = $servicio;
            $this->serviciosPackIDs[count($this->serviciosPack)] = $this->servicio;
        } else {
            $this->alert('warning', 'Este servicio ya se encuentra en este pack', [
                'position' => 'center',
                'timer' => 1000,
            ]);
        }
    }
    public function render()
    {
        return view('livewire.servicios_packs.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',

            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
            ]
        );

        // Guardar datos validados
        $packSave = ServicioPack::create($validatedData);

        // Alertas de guardado exitoso
        if ($packSave) {

            foreach ($this->serviciosPack as $servicio) {
                Servicio::where('id', $servicio["id"])->update(["id_pack" => $packSave->id]);
            }

            $this->alert('success', 'Paquete registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del usuario!', [
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
        return redirect()->route('servicios-packs.index');
    }
}
