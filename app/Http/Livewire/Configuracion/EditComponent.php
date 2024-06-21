<?php

namespace App\Http\Livewire\Configuracion;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\DepartamentosProveedores;
use Livewire\WithFileUploads;
use App\Models\Almacen;

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
    public $almacenes = [];
    public $almacenNombre;
    public $almacenDireccion;
    public $almacenHorario;
    public $editableAlmacen = [];
    public $newAlmacen = [
        'almacen' => '',
        'direccion' => '',
        'horario' => ''
    ];
    protected $rules = [
        'editableAlmacen.almacen' => 'required|string',
        'editableAlmacen.direccion' => 'required|string',
        'editableAlmacen.horario' => 'required|string',
        'newAlmacen.almacen' => 'required|string',
        'newAlmacen.direccion' => 'required|string',
        'newAlmacen.horario' => 'required|string',
    ];

    public function addAlmacen()
    {
        $this->validate([
            'newAlmacen.almacen' => 'required|string',
            'newAlmacen.direccion' => 'required|string',
            'newAlmacen.horario' => 'required|string',
        ]);

        Almacen::create($this->newAlmacen);

        $this->almacenes = Almacen::all();  // Refrescar la lista de almacenes
        $this->newAlmacen = [  // Limpiar el formulario de nuevo almacén
            'almacen' => '',
            'direccion' => '',
            'horario' => ''
        ];

        

        $this->alert('success', 'Almacén agregado con éxito');

    }

    public function mount($configuracion)
    {
        $this->configuracion = $configuracion;
        $this->cuenta = $configuracion->cuenta;
        $this->departamentos = DepartamentosProveedores::all();
        $this->firma = $configuracion->firma;
        if($this->firma != null){
            $this->hasImage = true;
        }
        $this->almacenes = Almacen::all();
        //dd($this->firma);
    }

    
    public function edit($id)
    {
        $this->editableAlmacen = $this->almacenes->find($id)->toArray();
    }

    public function saveAlmacen()
    {

        $almacen = Almacen::find($this->editableAlmacen['id']);
        $almacen->update($this->editableAlmacen);

        $this->almacenes = Almacen::all();  // Refrescar la lista de almacenes
        $this->editableAlmacen = [];  // Limpiar el campo editable
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
