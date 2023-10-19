<?php

namespace App\Http\Livewire\Clients;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Clients;

class CreateComponent extends Component
{
    use LivewireAlert;

    public $tipoCliente;

    // Cliente Empresa
    public $nameEmpresa;
    public $taxNumber;
    public $address;
    public $email;
    public $ciudad;
    public $province;
    public $postCode;
    
    // Cliente Particular
    public $nameCliente;
    public $firstSurname;
    public $lastSurname;
    public $dni;
    public $adressCliente;
    public $emailCliente;
    public $ciudadCliente;
    public $provinceCliente;
    public $postCodeCliente;


    // Renderizado del Componente
    public function render()
    {      
        return view('livewire.clients.create-component');
    }

    // Submit del cliente tipo particular
    public function submitCliente()
    {      
        // Validamos los datos recibidos
        $validatedData = $this->validate([
            'nameCliente' => 'required',
            'firstSurname' => 'required',
            'lastSurname' => 'required',
            'dni' => 'required', 
            'adressCliente' => 'required', 
            'ciudadCliente' => 'required', 
            'provinceCliente' => 'required', 
            'postCodeCliente' => 'required', 
        ]);
        // Establecemos el tipo de cliente para registrar
        $validatedData['tipoCliente'] = $this->tipoCliente;
        $validatedData['trato'] = null;
        // Creamos el cliente en la DB
        $clienteSave = Clients::create( $validatedData );
    
        if ($clienteSave) {
            $this->alert('success', 'Se ha registrado correctamente la empresa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
                ]);
        }else{
            $this->alert('error', 'No se ha podido guardar la información de la empresa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    // Submit del cliente tipo empresa
    public function submit()
    {      
        // Validamos los datos recibidos
        $validatedData = $this->validate([
            'nameEmpresa' => 'required',
            'taxNumber' => 'required', 
            'address' => 'required', 
            'ciudad' => 'required', 
            'province' => 'required', 
            'postCode' => 'required', 
        ]);
        // Establecemos el tipo de cliente para registrar
        $validatedData['tipoCliente'] = $this->tipoCliente;
        // Creamos el cliente en la DB
        $clienteSave = Clients::create( $validatedData );
    
        // Retornamos hacia la vista con un alert
        if ($clienteSave) {
            $this->alert('success', 'Se ha registrado correctamente la empresa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
                ]);
        }else{
            $this->alert('error', 'No se ha podido guardar la información de la empresa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function getListeners()
    {
        return [
            'confirmed'
        ];
    }

    public function confirmed()
    {
        return redirect()->route('clients.index');
    }

}
