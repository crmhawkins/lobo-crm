<?php

namespace App\Http\Livewire\StockMercaderia;

use App\Models\Mercaderia;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\StockMercaderia;
use App\Models\StockMercaderiaEntrante;
use PDF;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class IndexComponent extends Component

{
    use LivewireAlert;
    public $mercaderia;
    public $producto_seleccionado;
    public $producto_lotes;

    public function mount()
    {
        $this->mercaderia = Mercaderia::all();
    }
    public function render()
    {
        return view('livewire.stock-mercaderia.index-component', [
            'mercaderia' => $this->mercaderia,
        ]);
    }

    public function getCantidad($id)
    {
        return StockMercaderiaEntrante::where('mercaderia_id', $id)->get()->sum('cantidad');
    }

    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'setLotes'
        ];
    }
    public function formatFecha($fecha)
    {
        return Carbon::parse($fecha)->format('d/m/Y');
    }
    public function alertaGuardar()
    {
        $this->alert('warning', '¿Estás seguro? Comprueba que se han usado todos los códigos QR, o que los códigos por generar sean necesarios.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmed',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => false,
        ]);
    }
    public function confirmed()
    {
        // Do something
        return redirect()->route('stock-mercaderia.crearQR');
    }
}
