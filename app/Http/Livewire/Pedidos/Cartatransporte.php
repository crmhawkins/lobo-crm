<?php

namespace App\Http\Livewire\Pedidos;

use Livewire\Component;
use App\Models\Pedido;
use App\Models\CartaTransporte as CartaTransporteModel;
use Illuminate\Support\Facades\Storage;
//import carbon
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Configuracion;

class Cartatransporte extends Component
{


    public $remitente;
    public $cargador_contractual;
    public $operador_transporte;
    public $consignatario;
    public $lugar_entrega;
    public $lugar_fecha_carga;
    public $documentos_anexos;
    public $marca_numeros;
    public $numero_bultos;
    public $clases_embalaje;
    public $naturaleza;
    public $n_estadistico;
    public $peso_bruto;
    public $volumen;
    public $instrucciones;
    public $firma_transportista;
    public $vehiculo;
    public $porteadores_sucesivos;
    public $reembolso;
    public $lugar;
    public $fecha;
    public $precio_remitente;
    public $liquido_remitente;
    public $suplementos_remitente;
    public $gastos_remitente;
    public $precio_moneda;
    public $liquido_moneda;
    public $suplementos_moneda;
    public $gastos_moneda;
    public $precio_consignatario;
    public $liquido_consignatario;
    public $suplementos_consignatario;
    public $gastos_consignatario;
    public $total_remitente;
    public $total_moneda;
    public $total_consignatario;
    public $pedido_id;
    public $identificador;
    public $pedido;
    public $lugar_entrega_16;
    public $porte_pagado;
    public $porte_debido;
    public $formalizado;
    public $descuento_remitente;
    public $descuento_consignatario;
    public $descuento_moneda;
    public $remitente_tabla;
    public $moneda_tabla;
    public $consignatario_tabla;
    public $lugar_entrega_22;
    public $remitente_1;
    public $configuracion;
    public $firma;
    public $hasImage = false;



    public function mount(){
        $this->configuracion = Configuracion::first();  
        $this->pedido = Pedido::find($this->identificador);
        $this->pedido_id = $this->pedido->id;
        $this->firma = $this->configuracion->firma;
        if($this->firma != null){
            $this->hasImage = true;
        }
        $cartaTransporte = CartaTransporteModel::where('pedido_id' , $this->pedido->id)->first();
        $this->getData($cartaTransporte);
         
    }

    public function getData($cartaTransporte){

        if($cartaTransporte != null){
            $this->remitente = $cartaTransporte->remitente == 0 ? null : $cartaTransporte->remitente;
            $this->cargador_contractual = $cartaTransporte->cargador_contractual == 0 ? null : $cartaTransporte->cargador_contractual;
            $this->operador_transporte = $cartaTransporte->operador_transporte;
            $this->consignatario = $cartaTransporte->consignatario;
            $this->lugar_entrega = $cartaTransporte->lugar_entrega;
            $this->lugar_fecha_carga = $cartaTransporte->lugar_fecha_carga;
            $this->documentos_anexos = $cartaTransporte->documentos_anexos;
            $this->marca_numeros = $cartaTransporte->marca_numeros;
            $this->numero_bultos = $cartaTransporte->numero_bultos;
            $this->clases_embalaje = $cartaTransporte->clases_embalaje;
            $this->naturaleza = $cartaTransporte->naturaleza;
            $this->n_estadistico = $cartaTransporte->n_estadistico;
            $this->peso_bruto = $cartaTransporte->peso_bruto;
            $this->volumen = $cartaTransporte->volumen;
            $this->instrucciones = $cartaTransporte->instrucciones;
            $this->firma_transportista = $cartaTransporte->firma_transportista;
            $this->vehiculo = $cartaTransporte->vehiculo;
            $this->porteadores_sucesivos = $cartaTransporte->porteadores_sucesivos;
            $this->reembolso = $cartaTransporte->reembolso;
            $this->lugar = $cartaTransporte->lugar;
            $this->fecha = $cartaTransporte->fecha;
            $this->precio_remitente = $cartaTransporte->precio_remitente;
            $this->liquido_remitente = $cartaTransporte->liquido_remitente;
            $this->suplementos_remitente = $cartaTransporte->suplementos_remitente;
            $this->gastos_remitente = $cartaTransporte->gastos_remitente;
            $this->precio_moneda = $cartaTransporte->precio_moneda;
            $this->liquido_moneda = $cartaTransporte->liquido_moneda;
            $this->suplementos_moneda = $cartaTransporte->suplementos_moneda;
            $this->gastos_moneda = $cartaTransporte->gastos_moneda;
            $this->precio_consignatario = $cartaTransporte->precio_consignatario;
            $this->liquido_consignatario = $cartaTransporte->liquido_consignatario;
            $this->suplementos_consignatario = $cartaTransporte->suplementos_consignatario;
            $this->gastos_consignatario = $cartaTransporte->gastos_consignatario;
            $this->total_remitente = $cartaTransporte->total_remitente;
            $this->total_moneda = $cartaTransporte->total_moneda;
            $this->total_consignatario = $cartaTransporte->total_consignatario;
            $this->pedido_id = $cartaTransporte->pedido_id;
            $this->lugar_entrega_16 = $cartaTransporte->lugar_entrega_16;
            //dd($cartaTransporte->porte_debido);
            $this->porte_pagado = $cartaTransporte->porte_pagado == 1 ? true : false;
            $this->porte_debido = $cartaTransporte->porte_debido == 1 ? true : false;
            $this->formalizado = $cartaTransporte->formalizado;
            $this->descuento_remitente = $cartaTransporte->descuento_remitente;
            $this->descuento_consignatario = $cartaTransporte->descuento_consignatario;
            $this->descuento_moneda = $cartaTransporte->descuento_moneda;
            $this->remitente_tabla = $cartaTransporte->remitente_tabla;
            $this->moneda_tabla = $cartaTransporte->moneda_tabla;
            $this->consignatario_tabla = $cartaTransporte->consignatario_tabla;
            $this->lugar_entrega_22 = $cartaTransporte->lugar_entrega_22;
            $this->remitente_1 = $cartaTransporte->remitente_1;
        }

    }

    protected $listeners = ['save'];
    public function getListeners()
    {
        return [
            'save'
        ];
    }

    public function eliminar(){
        $cartaTransporteAEliminar = CartaTransporteModel::where('pedido_id' , $this->pedido->id)->first();
        if($cartaTransporteAEliminar){
            $cartaTransporteAEliminar->delete();
            $cartaTransporte = CartaTransporteModel::where('pedido_id' , $this->pedido->id)->first();
        }

        //recarga la pagina para que se actualice la informacion
        return redirect()->route('pedidos.cartatransporte', $this->pedido->id);
        


    }

    public function save(){
        //validate all data
        if($this->porte_pagado == true){
            $this->porte_pagado = 1;
        }else{
            $this->porte_pagado = 0;
        }

        if($this->porte_debido == true){
            $this->porte_debido = 1;
        }else{
            $this->porte_debido = 0;
        }
        
        if($this->firma_transportista != null && Str::of($this->firma_transportista)->contains('data:image/png;base64') ){
            $firmaRuta = 'firmas/' . Carbon::now()->format('Y-m-d_H-i-s') . '.png';
            Storage::disk('public')->put($firmaRuta, base64_decode(Str::of($this->firma_transportista)->after(',')));
            $this->firma_transportista = $firmaRuta;

        }

        

       $validatedData =  $this->validate([
            'remitente' => 'nullable',
            'cargador_contractual' => 'nullable',
            'operador_transporte' => 'nullable',
            'consignatario' => 'nullable',
            'lugar_entrega' => 'nullable',
            'lugar_fecha_carga' => 'nullable',
            'documentos_anexos' => 'nullable',
            'marca_numeros' => 'nullable',
            'numero_bultos' => 'nullable',
            'clases_embalaje' => 'nullable',
            'naturaleza' => 'nullable',
            'n_estadistico' => 'nullable',
            'peso_bruto' => 'nullable',
            'volumen' => 'nullable',
            'instrucciones' => 'nullable',
            'firma_transportista' => 'nullable',
            'vehiculo' => 'nullable',
            'porteadores_sucesivos' => 'nullable',
            'reembolso' => 'nullable',
            'lugar' => 'nullable',
            'fecha' => 'nullable',
            'precio_remitente' => 'nullable',
            'liquido_remitente' => 'nullable',
            'suplementos_remitente' => 'nullable',
            'gastos_remitente' => 'nullable',
            'precio_moneda' => 'nullable',
            'liquido_moneda' => 'nullable',
            'suplementos_moneda' => 'nullable',
            'gastos_moneda' => 'nullable',
            'precio_consignatario' => 'nullable',
            'liquido_consignatario' => 'nullable',
            'suplementos_consignatario' => 'nullable',
            'gastos_consignatario' => 'nullable',
            'total_remitente' => 'nullable',
            'total_moneda' => 'nullable',
            'total_consignatario' => 'nullable',
            'pedido_id' => 'nullable',
            'lugar_entrega_16' => 'nullable',
            'porte_pagado' => 'nullable',
            'porte_debido' => 'nullable',
            'formalizado' => 'nullable',
            'descuento_remitente' => 'nullable',
            'descuento_consignatario' => 'nullable',
            'descuento_moneda' => 'nullable',
            'remitente_tabla' => 'nullable',
            'moneda_tabla' => 'nullable',
            'consignatario_tabla' => 'nullable',
            'lugar_entrega_22' => 'nullable',
            'remitente_1' => 'nullable',
        ]);


        $cartaTransporte = CartaTransporteModel::where('pedido_id' , $this->pedido->id)->first();
        if(!$cartaTransporte){
           //create con validate
              $cartaTransporte = CartaTransporteModel::create($validatedData);
            
        }else{
            //update con validate
            $cartaTransporte->update($validatedData);
           
        }
        

    }

    public function render()
    {
        return view('livewire.pedidos.cartatransporte');
    }
}
