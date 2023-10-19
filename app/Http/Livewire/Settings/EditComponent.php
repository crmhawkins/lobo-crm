<?php

namespace App\Http\Livewire\Settings;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Settings;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class EditComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $iden;
    public $name;
    public $taxNumber;
    public $adress;
    public $ciudad;
    public $province;
    public $postCode;
    public $photo;

    public $empresa;


    public function render()
    {
        $this->empresa = Settings::whereNull('deleted_at')->first();
        $this->iden = $this->empresa->id;
        $this->name = $this->empresa->name;
        $this->taxNumber = $this->empresa->taxNumber;
        $this->adress = $this->empresa->adress;
        $this->ciudad = $this->empresa->ciudad;
        $this->province = $this->empresa->province;
        $this->postCode = $this->empresa->postCode;
        // $this->photo = $this->empresa->photo;

        return view('livewire.settings.edit-component');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'name' => 'required',
            'taxNumber' => 'required',
            'adress' => 'required',
            'ciudad' => 'required', 
            'province' => 'required', 
            'postCode' => 'required', 
            'photo' => 'image|max:4048', // 1MB Max
        ]);

        $imageName = random_int(0, 99999).'-img.'.$this->photo->getClientOriginalExtension();

        $this->photo->storeAs('assets', $imageName, 'public_local');

        $validatedData['photo'] = $imageName;

        $empresaSave = Settings::where('id', $this->iden)->update( $validatedData );
        if ($empresaSave) {
            $this->alert('success', 'Se ha actualido correctamente la empresa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
               ]);
        }else{
            $this->alert('error', 'No se ha podido actualizar la información de la empresa!', [
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
