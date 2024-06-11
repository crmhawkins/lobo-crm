<?php

namespace App\Http\Livewire\Mercaderia;

use App\Models\Presupuesto;
use App\Models\Mercaderia;
use App\Models\MercaderiaProduccion;
use App\Models\MercaderiaCategoria;
use App\Models\StockMercaderiaEntrante;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\ModificacionesMercaderia;
use App\Models\RoturaMercaderia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IndexComponent extends Component
{
    // public $search;
    use LivewireAlert;
    public $mercaderias;
    public $categorias;
    public $mercaderiaSeleccionadaId;
    public $mercaderiaSeleccionada;
    public $cantidad;
    public $motivo;
    
    public $stockMercaderiaEntrante;

    public $categoria_id;

    public function mount()
    {
        $this->mercaderias = Mercaderia::all();
        $this->categorias = MercaderiaCategoria::all();
        $this->stockMercaderiaEntrante = StockMercaderiaEntrante::all();
        // foreach($this->stockMercaderiaEntrante as $stock){
        //     $mercaderia = Mercaderia::find($stock->mercaderia_id);
        //     $producto = DB::table('productos_produccion')->where('producto_id', $mercaderia->id)->first();
        //     $orden = DB::table('orden_produccion')->where('id', $producto->orden_id)->first();
        //     //dd($orden);
        // }
    }

    public function comprobarStockMateriales()
    {
        // Obtiene la suma de cantidad de cada mercadería en stock
        $totalStockPorMercaderia = StockMercaderiaEntrante::selectRaw('mercaderia_id, SUM(cantidad) as total')
                                                           ->groupBy('mercaderia_id')
                                                           ->pluck('total', 'mercaderia_id');

        // Obtiene todas las mercaderías
        $todasLasMercaderias = Mercaderia::all();

        // Filtra las mercaderías que tienen stock agotado
        $materialesAgotados = $todasLasMercaderias->filter(function ($mercaderia) use ($totalStockPorMercaderia) {
            return isset($totalStockPorMercaderia[$mercaderia->id]) ? $totalStockPorMercaderia[$mercaderia->id] == 0 : true;
        });

        if ($materialesAgotados->isEmpty()) {
            $this->alert('success', 'Todos los materiales tienen stock disponible.');
        } else {
            $listaMateriales = $materialesAgotados->pluck('nombre')->toArray();
            $this->alert('warning', 'Materiales agotados: ' . implode(', ', $listaMateriales));
        }
    }


    public function changeMercaderiaSeleccionadaId($id){
        $this->mercaderiaSeleccionadaId = $id;
        
        $this->mercaderiaSeleccionada = Mercaderia::find($id);
        //dd($this->mercaderiaSeleccionada);
    }

    public function updateMateriales(){

        $query = Mercaderia::query();

        if($this->categoria_id != 0){
            $query->where('categoria_id', $this->categoria_id);
        }

        $this->mercaderias = $query->get();

        $this->emit('refreshComponent');    


    }

    public function addStock(){
        
        
        if($this->mercaderiaSeleccionada == null){
            $this->alert('warning', 'Seleccione una mercadería.');
            return;
        }

        if($this->cantidad == null){
            $this->alert('warning', 'Ingrese una cantidad.');
            return;
        }


        
        
        $update =  $this->updateStock('Suma');

        if($update){
            $this->alert('success', 'Stock actualizado correctamente.');
        }else{
            $this->alert('error', 'Error al actualizar el stock.');
        }

        $this->resetSeleccionados();
    }

    public function deleteStock(){
        
        
        if($this->mercaderiaSeleccionada == null){
            $this->alert('warning', 'Seleccione una mercadería.');
            return;
        }

        if($this->cantidad == null){
            $this->alert('warning', 'Ingrese una cantidad.');
            return;
        }

        $update =  $this->updateStock('Resta');

        if($update){
            $this->alert('success', 'Stock actualizado correctamente.');
        }else{
            $this->alert('error', 'Error al actualizar el stock.');
        }

        $this->resetSeleccionados();
    }

    public function roturaStock(){
        
        if($this->mercaderiaSeleccionada == null){
            $this->alert('warning', 'Seleccione una mercadería.');
            return;
        }

        if($this->cantidad == null){
            $this->alert('warning', 'Ingrese una cantidad.');
            return;
        }

        if($this->motivo == null){
            $this->alert('warning', 'Ingrese un motivo.');
            return;
        }

        $update =  $this->updateStock('Rotura');

        if($update){
            $this->alert('success', 'Stock actualizado correctamente.');
        }else{
            $this->alert('error', 'Error al actualizar el stock.');
        }

        $this->resetSeleccionados();
    }

    public function resetSeleccionados(){
        $this->mercaderiaSeleccionada = null;
        $this->mercaderiaSeleccionadaId = null;
        $this->cantidad = null;
        $this->motivo = null;

    }

    public function updateStock($tipo){
        $mercaderiaEntrante = StockMercaderiaEntrante::where('mercaderia_id', $this->mercaderiaSeleccionada->id)->first();
        if(!$mercaderiaEntrante){
            return false;
        }

        if($tipo == 'Resta' || $tipo == 'Rotura'){
            if($mercaderiaEntrante->cantidad < $this->cantidad){
                $this->alert('warning', 'No hay suficiente stock.');
                return false;
            }
            $mercaderiaEntranteNew = StockMercaderiaEntrante::create([
                'mercaderia_id' => $this->mercaderiaSeleccionada->id,
                'cantidad' => -abs($this->cantidad),
                'tipo' => 'Entrante',
            ]);

        }else{
            $mercaderiaEntranteNew = StockMercaderiaEntrante::create([
                'mercaderia_id' => $this->mercaderiaSeleccionada->id,
                'cantidad' => abs($this->cantidad),
                'tipo' => 'Entrante',
            ]);
        }
        
        //dd($mercaderiaEntrante);
       if($mercaderiaEntranteNew){
            if($tipo == 'Suma' || $tipo == 'Resta'){
                $motivoMercaderia  = ModificacionesMercaderia::create([
                    'mercaderia_id' => $this->mercaderiaSeleccionada->id,
                    'stock_mercaderia_entrante_id' => $mercaderiaEntranteNew->id,
                    'motivo' => $this->motivo ?? 'Modificacion de stock',
                    'cantidad' => abs($this->cantidad),
                    'user_id' => Auth::user()->id,
                    'tipo' => $tipo,
                    'fecha' => Carbon::now(),
                ]);
            }else{

                $motivoMercaderia = RoturaMercaderia::create([
                    'mercaderia_id' => $this->mercaderiaSeleccionada->id,
                    'stock_mercaderia_entrante_id' => $mercaderiaEntranteNew->id,
                    'motivo' => $this->motivo,
                    'cantidad' => abs($this->cantidad),
                    'user_id' => Auth::user()->id,
                    'fecha' => Carbon::now(),
                ]);

            }
            
       }

       return $mercaderiaEntranteNew;

    }

    public function updated($propertyName)
    {
        if (
            $propertyName == 'categoria_id' 

        ) {
            $this->updateMateriales();
        }
    }

    public function getCantidad($id)
    {
        return StockMercaderiaEntrante::where('mercaderia_id', $id)->get()->sum('cantidad');
    }
    public function render()
    {
        return view('livewire.mercaderia.index-component');
    }

    public function getCantidadProduccion($id)
    {
        return MercaderiaProduccion::where('mercaderia_id', $id)->get()->sum('cantidad');
    }

    public function getCategoria($id){
        return $this->categorias->where('id', $id)->first()->nombre;
    }

    public function cambioCategoria(){
        if($this->categoria_id == 0){
            $this->mercaderias = Mercaderia::all();
        }else{
            $this->mercaderias = Mercaderia::where('categoria_id', $this->categoria_id)->get();
        }
        $this->emit('refreshComponent');
    }

    public function getListeners()
    {
        return [
            'cambioCategoria',
            'comprobarStockMateriales',
            'refreshComponent' => '$refresh',
        ];
    }

}
