<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Alumno;
use App\Models\Cursos;
use App\Models\Productos;
use App\Models\Pedido;
use App\Models\Clients;
use App\Models\Facturas;
use App\Policies\ClientsEmailPolicy;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Alertas;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $idpedido;
    public $numero_factura; // 0 por defecto por si no se selecciona ninguno
    public $fecha_emision;
    public $fecha_vencimiento;
    public $descripcion;
    public $estado = "Pendiente";
    public $metodo_pago = "No Pagado";

    public $pedido;
    public $precio;
    public $pedido_id;
    public $cliente;
    public $clientes;
    public $cliente_id;
    public $producto_id;
    public $productos;
    public $cantidad;
    public $descuento;
    public $isFacturaRectificativa = false;
    public $tipo;
    public $observacionesDescarga;

    public function mount()
    {

        if (isset($this->idpedido)){
            $this->pedido_id = $this->idpedido;
            $this->pedido = Pedido::find($this->idpedido);
            $this->cliente_id = $this->pedido->cliente_id;
            $this->cliente = Clients::find($this->cliente_id);
            $this->observacionesDescarga = $this->cliente->observaciones;
            $diasVencimiento = $this->cliente->vencimiento_factura_pref;
            $this->fecha_vencimiento = Carbon::now()->addDays($diasVencimiento)->format('Y-m-d');
            $this->metodo_pago = $this->cliente->forma_pago_pref;
            $this->precio = $this->pedido->precio;
            if($this->pedido->descuento){
                $this->descuento = $this->pedido->porcentaje_descuento;
            }
        }
        $this->productos = Productos::where('tipo_precio',5)->get();
        $this->clientes = Clients::where('estado', 2)->get();
        $year = Carbon::now()->format('y'); // Esto obtiene el año en formato de dos dígitos, por ejemplo, "24" para 2024.
        $lastInvoice = Facturas::whereYear('created_at', Carbon::now()->year)->max('numero_factura');
        if ($lastInvoice) {
            // Extrae el número secuencial de la última factura del año y lo incrementa
            $lastNumber = intval(substr($lastInvoice, 3)) + 1; // Asume que el formato es siempre "F24XXXX"
        } else {
            if($year = 24 ){
                $lastNumber = 150;
            }else{
                $lastNumber = 1;
            }

        }
        // Genera el nuevo número de factura con relleno para asegurar 4 dígitos
        $this->numero_factura = 'F' . $year . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
        $this->fecha_emision = Carbon::now()->format('Y-m-d');
    }

    public function getNumeroFactura(){
        $year = Carbon::now()->format('y'); // Esto obtiene el año en formato de dos dígitos, por ejemplo, "24" para 2024.
        if($this->isFacturaRectificativa){
            $lastInvoice = Facturas::whereYear('created_at', Carbon::now()->year)->where('tipo', 2)->max('numero_factura');

            if ($lastInvoice) {
                // Extrae el número secuencial de la última factura del año y lo incrementa
                $lastNumber = intval(substr($lastInvoice, 3)) + 1; // Asume que el formato es siempre "F24XXXX"
            } else {
                $lastNumber = 1;
            }
        }else{
            $lastInvoice = Facturas::whereYear('created_at', Carbon::now()->year)->max('numero_factura');

            if ($lastInvoice) {
                // Extrae el número secuencial de la última factura del año y lo incrementa
                $lastNumber = intval(substr($lastInvoice, 3)) + 1; // Asume que el formato es siempre "F24XXXX"
            } else {
                if($year = 24 ){
                    $lastNumber = 150;
                }else{
                    $lastNumber = 1;
                }
            }
           
        }
        
        
        if($this->isFacturaRectificativa){
            $this->numero_factura = 'CN' . $year . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
        }else{
            $this->numero_factura = 'F' . $year . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
        }
        //dd("prueba");

    }

    public function render()
    {
        
        $this->getNumeroFactura();
        return view('livewire.facturas.create-component');
    }

    


    public function calculoPrecio()
    {
        if(isset($this->cantidad) && isset($this->producto_id)){
           $producto = $this->productos->find($this->producto_id);
           $this->precio = $producto->precio * $this->cantidad;
        }
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        
        if($this->isFacturaRectificativa){
            $this->tipo = 2;
            // Validación de datos
            $validatedData = $this->validate(
                [
                    'numero_factura' => 'required',
                    'cliente_id' => 'required',
                    'pedido_id' => 'nullable',
                    'fecha_emision' => 'required',
                    'fecha_vencimiento' => '',
                    'descripcion' => '',
                    'estado' => 'nullable',
                    'precio' => 'nullable',
                    'metodo_pago' => 'nullable',
                    'producto_id' => 'nullable',
                    'cantidad' => 'nullable',
                    'descuento' => 'nullable',
                    'tipo' => 'required',

                ],
                // Mensajes de error
                [
                    'numero_factura.required' => 'Indique un nº de factura.',
                    'fecha_emision.required' => 'Ingrese una fecha de emisión',
                    'id_pedido.min' => 'Seleccione un pedido',
                ]
            );
        }else{
            // Validación de datos
            $validatedData = $this->validate(
                [
                    'numero_factura' => 'required',
                    'cliente_id' => 'required',
                    'pedido_id' => 'nullable',
                    'fecha_emision' => 'required',
                    'fecha_vencimiento' => '',
                    'descripcion' => '',
                    'estado' => 'nullable',
                    'precio' => 'nullable',
                    'metodo_pago' => 'nullable',
                    'producto_id' => 'nullable',
                    'cantidad' => 'nullable',
                    'descuento' => 'nullable',

                ],
                // Mensajes de error
                [
                    'numero_factura.required' => 'Indique un nº de factura.',
                    'fecha_emision.required' => 'Ingrese una fecha de emisión',
                    'id_pedido.min' => 'Seleccione un pedido',
                ]
            );
        }
        

        // Guardar datos validados
        $facturasSave = Facturas::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 17, $facturasSave->id));
        $pedidosSave =false;
        // Alertas de guardado exitoso
        if (isset($this->idpedido)){
        $pedidosSave = $this->pedido->update(['estado' => 5]);
        }
        if ($facturasSave) {
            if($pedidosSave){
                Alertas::create([
                    'user_id' => 13,
                    'stage' => 3,
                    'titulo' => 'Estado del Pedido: Entregado ',
                    'descripcion' => 'El pedido nº ' . $this->pedido->id . ' ha sido entregado',
                    'referencia_id' => $this->pedido->id,
                    'leida' => null,
                ]);
            }
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
}
