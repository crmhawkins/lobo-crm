<?php

namespace App\Http\Livewire\Configuracion;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\DepartamentosProveedores;
use Livewire\WithFileUploads;

class EditComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $cuenta;
    public $configuracion;
    public $departamentos = [];
    public $nombreDepartamento;
    public $firma;
    public $hasImage = false;


    public function mount($configuracion)
    {
        $this->configuracion = $configuracion;
        $this->cuenta = $configuracion->cuenta;
        $this->departamentos = DepartamentosProveedores::all();
        $this->firma = $configuracion->firma;
        if($this->firma != null){
            $this->hasImage = true;
        }
        //dd($this->firma);
    }


    public function enviarWhatsappPrueba(){
        $phone ="+34640181164";
        $data = [];

        enviarMensajeWhatsApp('hello_world',$data , $phone);
        $this->alert('success', 'Mensaje enviado con éxito');

    }


   public function saveFirma(){


    if(isset($this->firma))
        {

            $name = md5($this->firma . microtime()) . '.' . $this->firma->extension();

            $this->firma->storePubliclyAs('public', 'photos/' . $name);

            $validatedData['firma'] = $name;
            $this->configuracion->update($validatedData);
            $this->firma = $this->configuracion->firma;
            //refresh render
            $this->mount($this->configuracion);
        }
        
        
   }


    public function addDepartamento(){
        $this->validate([
            'nombreDepartamento' => 'required'
        ]);
        $departamento = DepartamentosProveedores::create([
            'nombre' => $this->nombreDepartamento,
            'descripcion' => ''
        ]);

        $this->departamentos = DepartamentosProveedores::all();
    }

    public function removeDepartamento($id){
        $departamento = DepartamentosProveedores::find($id);
        $departamento->delete();
        $this->departamentos = DepartamentosProveedores::all();
    }

    public function update()
    {
        $this->validate([
            'cuenta' => 'required'
        ]);

        $this->configuracion->update([
            'cuenta' => $this->cuenta
        ]);

        $this->alert('success', 'Configuración actualizada con éxito');
    }


     //function getListeners update

    protected $listeners = [
        'update' => 'update',
        'save' => 'save'
    ];

    public function getListeners()
    {
        return [
            'update' => 'update',
            'save' => 'save'
        ];
    }




    public function render()
    {
        
        return view('livewire.configuracion.edit-component');
    }
}
