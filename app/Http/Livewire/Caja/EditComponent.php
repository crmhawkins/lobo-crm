<?php

namespace App\Http\Livewire\Caja;

use App\Models\Caja;
use App\Models\Pedido;
use App\Models\Proveedores;
use App\Models\Clients;
use App\Models\Facturas;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;



class EditComponent extends Component
{
	    use LivewireAlert;

    public $identificador;
    public $tipo_movimiento;
    public $metodo_pago;
    public $importe;
    public $descripcion;
    public $poveedores;
    public $poveedor_id;
    public $fecha;
    public $clientes;
    public $categorias;
    public $pedido_id;
    public $pedido;
    public $facturas;
    public $estado;
    public $banco;




    public function mount()
    {
        $caja = Caja::find($this->identificador);
        $this->poveedores = Proveedores::all();
        $this->facturas = Facturas::all();
        $this->clientes = Clients::all();
        $this->metodo_pago = $caja->metodo_pago;
        $this->descripcion = $caja->descripcion;
        $this->importe = $caja->importe;
        $this->poveedor_id = $caja->poveedor_id;
        $this->pedido_id = $caja->pedido_id;
        $this->fecha = $caja->fecha;
        $this->estado = $caja->estado;
        $this->tipo_movimiento = $caja->tipo_movimiento;
        $this->banco = $caja->banco;


    }
    public function getCliente($id)
    {
         return $this->clientes->firstWhere('id', $id)->nombre;
    }
    public function render()
    {
        return view('livewire.caja.edit-component');
    }

// Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'metodo_pago' => 'required',
            'importe' => 'required',
            'poveedor_id' => 'nullable',
            'pedido_id' => 'nullable',
            'fecha' => 'required',
            'tipo_movimiento' => 'required',
            'descripcion' => 'required',
            'estado' => 'nullable',


        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
            ]);

        // Encuentra el identificador
        $caja = Caja::find($this->identificador);

        // Guardar datos validados
        $tipoSave = $caja->update([
            'metodo_pago' => $this->metodo_pago,
            'importe' => $this->importe,
            'pedido_id' => $this->pedido_id,
            'poveedor_id' => $this->poveedor_id,
            'fecha' => $this->fecha,
            'tipo_movimiento' => $this->tipo_movimiento,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado

        ]);
        event(new \App\Events\LogEvent(Auth::user(), 53, $caja->id));

        if ($tipoSave) {
            $this->alert('success', '¡Movimiento de caja actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del movimiento de caja!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Movimiento de caja actualizado correctamente.');

        $this->emit('confirmed');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el movimiento de caja? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);

    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete',
            'update'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('caja.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $Caja = Caja::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 54, $Caja->id));
        $Caja->delete();
        return redirect()->route('caja.index');

    }
}
