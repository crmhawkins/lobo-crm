<?php

namespace App\Http\Livewire\Clients;

use Livewire\Component;
use App\Models\Clients;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

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

    public $tipoCliente;

    public function mount(){
        $cliente = Clients::find($this->identificador);

        if ($cliente->tipoCliente == 2) {
            $this->nameEmpresa = $cliente->nameEmpresa;
            $this->taxNumber = $cliente->taxNumber;
            $this->address = $cliente->address;
            $this->email = $cliente->email;
            $this->ciudad = $cliente->ciudad;
            $this->province = $cliente->province;
            $this->postCode = $cliente->postCode;
            $this->tipoCliente = $cliente->tipoCliente;
        } else {
            $this->nameCliente = $cliente->nameCliente;
            $this->firstSurname = $cliente->firstSurname;
            $this->lastSurname = $cliente->lastSurname;
            $this->dni = $cliente->dni;
            $this->adressCliente = $cliente->adressCliente;
            $this->emailCliente = $cliente->emailCliente;
            $this->ciudadCliente = $cliente->ciudadCliente;
            $this->provinceCliente = $cliente->provinceCliente;
            $this->postCodeCliente = $cliente->postCodeCliente;
            $this->tipoCliente = $cliente->tipoCliente;

        }

    }
    public function render()
    {
        
        return view('livewire.clients.edit-component');
    }

    public function submitCliente()
    {      
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
        $validatedData['tipoCliente'] = $this->tipoCliente;

        $empresaSave = Clients::where('id', $this->identificador)->update( $validatedData );
    
        if ($empresaSave) {
            $this->alert('success', 'Se ha actualizado correctamente el cliente!', [
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
    
    public function submit()
    {      
        $validatedData = $this->validate([
            'nameEmpresa' => 'required',
            'taxNumber' => 'required', 
            'address' => 'required', 
            'ciudad' => 'required', 
            'province' => 'required', 
            'postCode' => 'required', 
        ]);

        $empresaSave = Clients::where('id', $this->identificador)->update( $validatedData );
    
        if ($empresaSave) {
            $this->alert('success', 'Se ha actualizado correctamente el cliente!', [
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
        // Do something
        return redirect()->route('clients.index');

    }

}
