<?php

namespace App\Http\Livewire\Settings;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Settings;

class CreatedComponent extends Component
{
    use LivewireAlert;

    public $name;
    public $taxNumber;
    public $adress;
    public $ciudad;
    public $province;
    public $postCode;


    public function render()
    {
        return view('livewire.settings.created-component');
    }

    public function submit()
    {
        $validatedData = $this->validate([
            'name' => 'required',
            'taxNumber' => 'required',
            'adress' => 'required',
            'ciudad' => 'required', 
            'province' => 'required', 
            'postCode' => 'required', 
        ]);

        $empresaSave = Settings::create( $validatedData );

        if ($empresaSave) {
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
        // Do something
        return redirect()->route('settings.index');

    }
}
