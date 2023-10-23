<?php

namespace App\Http\Livewire\Almacen;

use App\Models\Alumno;
use App\Models\Clients;
use App\Models\Cursos;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\Albaran;
use App\Models\ProductoLote;
use App\Models\Productos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $identificador;
    public $pedido;
    public $pedido_id;
    public $num_albaran;
    public $cliente;
    public $fecha;
    public $fecha_vencimiento;
    public $descripcion;
    public $productos;
    public $lotes;
    public $clientes;
    public $total_factura;
    public $productos_pedido = [];
    public $estado = "Pendiente";
    public $observaciones;
    public function mount()
    {
        $this->pedido = Pedido::find($this->identificador);
        $this->pedido_id = $this->pedido->id;
        $this->cliente = Clients::where('id', $this->pedido->cliente_id)->first();
        $this->productos = Productos::all();
        $this->lotes = ProductoLote::all();
        $this->clientes = Clients::all();
        $this->num_albaran = Albaran::count() + 1;
        $this->fecha = Carbon::now()->format('Y-m-d');
        $productos = DB::table('productos_pedido')->where('pedido_id', $this->pedido_id)->get();
        foreach ($productos as $producto) {
            $this->productos_pedido[] = [
                'id' => $producto->id,
                'producto_lote_id' => $producto->producto_lote_id,
                'unidades_old' => $producto->unidades,
                'unidades' => 0,
            ];
        }
    }

    public function render()
    {
        return view('livewire.almacen.create-component');
    }


    // Al hacer submit en el formulario
    public function submit()
    {
        $this->total_factura = $this->pedido->precio;
        // Validación de datos
        $validatedData = $this->validate(
            [
                'num_albaran' => 'required',
                'pedido_id' => 'required|numeric|min:1',
                'fecha' => 'required',
                'observaciones' => 'nullable',
                'total_factura' => '',
            ],
            // Mensajes de error
            [
                'num_albaran.required' => 'Indique un nº de factura.',
                'fecha.required' => 'Ingrese una fecha de emisión',
                'pedido_id.min' => 'Seleccione un pedido',
            ]
        );

        $pedido = Pedido::firstWhere('id', $this->pedido_id)->update(['estado' => 5]);

        // Guardar datos validados
        $facturasSave = Albaran::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 17, $facturasSave->id));

        // Alertas de guardado exitoso
        if ($facturasSave) {
            $this->alert('success', 'Factura registrada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la factura!', [
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
            'submit',
            'destroy',
            'listarPedido',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('facturas.index');
    }
    public function getNombreTabla($id)
    {
        $producto_id = $this->lotes->where('id', $id)->first()->producto_id;
        $nombre_producto = $this->productos->where('id', $producto_id)->first()->nombre;
        return $nombre_producto;
    }
    public function getNombreLoteTabla($id)
    {
        $producto_id = $this->lotes->where('id', $id)->first()->lote_id;
        return $producto_id;
    }
    public function alertaGuardar()
    {
        $this->alert('warning', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'submit',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

}
