<?php

namespace App\Http\Livewire\Incidencias;

use Livewire\Component;
use App\Models\Incidencias;
use App\Models\PedidosIncidencias;
use Livewire\WithPagination;

class Todas extends Component
{
    use WithPagination;

    public $activeTab = 'incidencias';
    protected $paginationTheme = 'bootstrap';

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.incidencias.todas', [
            'incidencias' => Incidencias::withTrashed()->paginate(10),
            'incidenciasPedidos' => PedidosIncidencias::withTrashed()->paginate(10),
        ]);
    }
}
