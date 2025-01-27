<?php

namespace App\Http\Livewire\Configuracion;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\DepartamentosProveedores;
use Livewire\WithFileUploads;
use App\Models\Almacen;
use App\Models\Configuracion;
use App\Models\Logs;
use App\Models\Retencion;
use Carbon\Carbon;

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
    public $texto_factura;
    public $texto_pedido;
    public $texto_albaran;
    public $texto_email;
    public $logs;
    public $retenciones = [];
    public $nombre_retencion;
    public $porcentaje_retencion;
    public $dias_retencion;

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
         // Obtener los logs del mes y año actual
         $this->logs = Logs::whereYear('date', Carbon::now()->year)
         ->whereMonth('date', Carbon::now()->month)
         ->orderBy('date', 'desc')
         ->get();
        $this->configuracion = $configuracion;
        $this->cuenta = $configuracion->cuenta;
        $this->departamentos = DepartamentosProveedores::all();
        $this->firma = $configuracion->firma;
        if($this->firma != null){
            $this->hasImage = true;
        }
        $this->almacenes = Almacen::all();
        $this->texto_factura = $configuracion->texto_factura;
        $this->texto_pedido = $configuracion->texto_pedido;
        $this->texto_albaran = $configuracion->texto_albaran;
        $this->texto_email = $configuracion->texto_email;

        $this->retenciones = Retencion::all();
        //dd($this->firma);
    }

    public function addRetencion(){
        $this->validate([
            'nombre_retencion' => 'required',
            'porcentaje_retencion' => 'required',
            'dias_retencion' => 'required'
        ]);
        Retencion::create([
            'nombre' => $this->nombre_retencion,
            'porcentaje' => $this->porcentaje_retencion,
            'dias_retencion' => $this->dias_retencion
        ]);
        $this->retenciones = Retencion::all();
        $this->nombre_retencion = '';
        $this->porcentaje_retencion = '';
        $this->dias_retencion = '';
        $this->alert('success', 'Retención añadida con éxito');
    }

    public function deleteRetencion($id){
        $retencion = Retencion::find($id);
        $retencion->delete();
        $this->retenciones = Retencion::all();
        $this->alert('success', 'Retención eliminada con éxito');
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
            'cuenta' => $this->cuenta,
            'texto_factura' => $this->texto_factura,
            'texto_pedido' => $this->texto_pedido,
            'texto_albaran' => $this->texto_albaran,
            'texto_email' => $this->texto_email

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
