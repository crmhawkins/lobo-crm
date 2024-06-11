<?php

namespace App\Http\Livewire\Mercaderia;


use Livewire\Component;
use App\Models\Mercaderia;
use App\Models\MercaderiaCategoria;
use App\Models\StockMercaderiaEntrante;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class Historial extends Component
{
    public $mercaderias;
    public $categorias;
    public $stockMercaderiaEntrante;
    public $historial = [];
    public $isEntrada = false;

    public function mount()
    {   
        $this->mercaderias = Mercaderia::all();
        $this->categorias = MercaderiaCategoria::all();
        $this->stockMercaderiaEntrante = StockMercaderiaEntrante::all();
        

        
    }

    public function updated($field)
    {
        
    }

    public function render()
    {
        $this->historial = [];
        $no_products = [];
        $no_mercaderia = [];
        $no_orden = [];
        if($this->isEntrada){
            $this->stockMercaderiaEntrante = StockMercaderiaEntrante::where('tipo', 'Entrante')->get();
        }else{
            $this->stockMercaderiaEntrante = StockMercaderiaEntrante::where('tipo', 'Saliente')->get();
        }
        foreach($this->stockMercaderiaEntrante as $stock){
            $mercaderia = Mercaderia::find($stock->mercaderia_id);
            if(!isset($mercaderia)){
                $no_mercaderia[] = $stock;
                continue;
            }
            if($stock->tipo == "Entrante"){
                $this->historial[]= [
                    'mercaderia' => $mercaderia->nombre,
                    'cantidad' => $stock->cantidad,
                    'fecha' => Carbon::parse($stock->created_at)->format('d-m-Y'),
                    'order_date' => Carbon::parse($stock->created_at)->format('Ymd'),
                    'orden' => 'Entrada',
                    'tipo' => $stock->tipo
                ];
                continue;
            }
            $producto = DB::table('mercaderia_produccion')->where('mercaderia_id', $mercaderia->id)->where('cantidad', abs($stock->cantidad))->where('created_at' , $stock->created_at)->first();

            if(!isset($producto)){
                $no_products[] = $stock;
                continue;    
            }
            $orden = DB::table('orden_produccion')->where('id', $producto->orden_id)->first();
            if($orden){
                $this->historial[] = [
                    'mercaderia' => $mercaderia->nombre,
                    'cantidad' => $stock->cantidad,
                    'fecha' => Carbon::parse($stock->created_at)->format('d-m-Y'),
                    'order_date' => Carbon::parse($stock->created_at)->format('Ymd'),
                    'orden' => $orden->numero,
                    'tipo' => $stock->tipo
                ];
                
            }else{
                $no_orden[] = $stock;
            }
            
        }
        //dd($this->historial);
        //dd($no_products, $no_mercaderia, $no_orden);

        return view('livewire.mercaderia.historial');
    }
}
