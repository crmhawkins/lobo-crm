<?php

namespace App\Http\Livewire\Operaciones;

use App\Models\Settings;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\Proveedores;
use App\Models\Facturas;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Caja;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Delegacion;
use App\Models\FacturasCompensadas;
use Livewire\WithPagination;

class Pagares extends Component
{
    use LivewireAlert, WithPagination;





    public function mount()
    {
       
    }

   

    public function render()
    {
        return view('livewire.operaciones.pagares');
    }
   



}
