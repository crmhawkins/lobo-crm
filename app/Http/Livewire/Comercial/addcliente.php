<?php

namespace App\Http\Livewire\Comercial;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\ClientesComercial;
use App\Models\Delegacion;
use App\Models\Productos;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Clients;


class addcliente extends Component
{
    // public $search;
    use LivewireAlert;
    public $nombre;
    public $cif;
    public $direccion;
    public $provincia;
    public $localidad;
    public $cod_postal;
    public $telefono;
    public $email;
    public $distribuidores;
    public $distribuidor_id;
    public $delegaciones;
    public $delegacion_id;



    public function render()
    {

        return view('livewire.comercial.addcliente');
    }


    public function mount()
    {
        //array asociativo donde cada producto es una clave y el precio es el valor
        $this->distribuidores = Clients::all();
        $this->delegaciones = Delegacion::all();
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
        $clienteSave = ClientesComercial::create([
            'comercial_id' => auth()->user()->id,
            'nombre' => $this->nombre,
            'cif' => $this->cif,
            'direccion' => $this->direccion,
            'provincia' => $this->provincia,
            'localidad' => $this->localidad,
            'cod_postal' => $this->cod_postal,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'distribuidor_id' => $this->distribuidor_id,
            'delegacion_id' => $this->delegacion_id,
        ]);

        if ($clienteSave) {
            $this->alert('success', '¡Cliente registrado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del cliente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        // Alertas de guardado exitoso
        // if ($clienteSave) {

        //     if(isset($this->anotacionesProximoPedido) && $this->anotacionesProximoPedido != ""){
        //         $anotacion = new AnotacionesClientePedido();
        //         $anotacion->cliente_id = $clienteSave->id;
        //         $anotacion->anotacion = $this->anotacionesProximoPedido;
        //         $anotacion->save();
        //     }

        //     Alertas::create([
        //         'user_id' => 13,
        //         'stage' => 1,
        //         'titulo' => 'Revisión Pendiente: Nuevo Cliente',
        //         'descripcion' => 'Nuevo cliente a la espera de aprobación: ' . $clienteSave->nombre,
        //         'referencia_id' => $clienteSave->id,
        //         'leida' => null,
        //     ]);

        //     $dGeneral = User::where('id', 13)->first();
        //     $administrativo1 = User::where('id', 17)->first();
        //     $administrativo2 = User::where('id', 18)->first();

        //     $data = [['type' => 'text', 'text' => $clienteSave->nombre]];
        //     $buttondata = [$clienteSave->id];

        //     if(isset($dGeneral) && $dGeneral->telefono != null){
        //         $phone = '+34'.$dGeneral->telefono;
        //         enviarMensajeWhatsApp('cliente_pendiente', $data, $buttondata, $phone);
        //     }

        //     if(isset($administrativo1) && $administrativo1->telefono != null){
        //         $phone = '+34'.$administrativo1->telefono;
        //         enviarMensajeWhatsApp('cliente_pendiente', $data, $buttondata, $phone);
        //     }

        //     if(isset($administrativo2) && $administrativo2->telefono != null){
        //         $phone = '+34'.$administrativo2->telefono;
        //         enviarMensajeWhatsApp('cliente_pendiente', $data, $buttondata, $phone);
        //     }




        //     $this->alert('success', '¡Cliente registrado correctamente!', [
        //         'position' => 'center',
        //         'timer' => 3000,
        //         'toast' => false,
        //         'showConfirmButton' => true,
        //         'onConfirmed' => 'confirmed',
        //         'confirmButtonText' => 'ok',
        //         'timerProgressBar' => true,
        //     ]);
        // } else {
        //     $this->alert('error', '¡No se ha podido guardar la información del cliente!', [
        //         'position' => 'center',
        //         'timer' => 3000,
        //         'toast' => false,
        //     ]);
        // }
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
        return redirect()->route('comercial.clientes');
    }
}
