<?php

namespace App\Http\Livewire\Mercaderia;

use App\Models\Presupuesto;
use App\Models\Mercaderia;
use App\Models\MercaderiaProduccion;
use App\Models\MercaderiaCategoria;
use App\Models\StockMercaderiaEntrante;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class IndexComponent extends Component
{
    // public $search;
    use LivewireAlert;
    public $mercaderias;
    public $categorias;

    public $categoria_id;

    public function mount()
    {
        $this->mercaderias = Mercaderia::all();
        $this->categorias = MercaderiaCategoria::all();
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
