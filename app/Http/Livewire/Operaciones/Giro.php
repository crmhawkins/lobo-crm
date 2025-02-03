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
use App\Models\Bancos;
use App\Models\GiroBancario;

class Giro extends Component
{
    use LivewireAlert, WithPagination;

    public $mes;
    public $anio;
    public $facturas;
    public $bancos;
    public $editing = [];
    public $giroData = [];

    protected $listeners = ['updatedMes', 'updatedAnio', 'saveGiro'];

    public function mount()
    {
        $this->mes = Carbon::now()->month;
        $this->anio = Carbon::now()->year;
        $this->obtenerFacturas();
        $this->bancos = Bancos::all();

        // Inicializar giroData con los datos existentes
      
    }

    public function obtenerFacturas()
    {

        $this->facturas = Facturas::where('tipo', null)
        ->where('metodo_pago', 'giro_bancario')
        ->whereMonth('fecha_emision', $this->mes)
        ->whereYear('fecha_emision', $this->anio)
        ->get();


        foreach ($this->facturas as $factura) {
            if ($factura->giro_bancario) {
                // dd($factura->giro_bancario);
                $this->giroData[$factura->id] = [
                    'banco_id' => $factura->giro_bancario->banco_id,
                    'fecha_programacion' => $factura->giro_bancario->fecha_programacion,
                    'estado' => $factura->giro_bancario->estado
                ];
            }
        }


    }

    public function editGiro($facturaId)
    {
        $this->editing[$facturaId] = true;
    }

    public function saveGiro($facturaId)
    {
        if (!isset($this->giroData[$facturaId])) {
            $this->alert('error', 'Por favor, complete los datos del giro bancario.');
            return;
        }
        

        $data = $this->giroData[$facturaId];

        $giro = GiroBancario::updateOrCreate(
            ['factura_id' => $facturaId],
            [

                'banco_id' => $data['banco_id'] ?? null,
                'fecha_programacion' => $data['fecha_programacion'] ?? null,
                'estado' => $data['estado'] ?? null
            ]
        );

        // dd($giro);

        $this->editing[$facturaId] = false;
        $this->obtenerFacturas();
        $this->alert('success', 'Giro bancario guardado correctamente!');
    }

    public function updatedMes($value)
    {
        $this->mes = $value;
        $this->obtenerFacturas();
    }

    public function updatedAnio($value)
    {
        $this->anio = $value;
     $this->obtenerFacturas();
    }

    public function render()
    {
        return view('livewire.operaciones.giro');
    }
}
