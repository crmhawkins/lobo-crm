<?php

namespace App\Http\Livewire\Operaciones;

use App\Models\Settings;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\Proveedores;
use App\Models\Facturas;
use App\Models\Pagares as PagaresModel;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Caja;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Delegacion;
use App\Models\FacturasCompensadas;
use Livewire\WithPagination;
use App\Models\Bancos;

class Pagares extends Component
{
    use LivewireAlert, WithPagination;

    public $mes;
    public $anio;
    public $nPagos = [];
    public $caja_id;
    public $pagares;
    public $cajas;
    public $bancos;

    public function mount()
    {
        $this->mes = Carbon::now()->month;
        $this->anio = Carbon::now()->year;
        $this->caja_id = Caja::first()->id;
        $this->bancos = Bancos::all();

        // Obtener todas las cajas con sus pagares
        $this->cajas = Caja::with([
            'pagares',
            'proveedor',
            'facturasCompensadas.factura'
        ])
        ->whereIn('metodo_pago', ['Pagare', 'PAGARÉ', 'pagare', 'Pagaré', 'PAGARE'])
        ->whereMonth('fecha', '=', $this->mes)
        ->whereYear('fecha', '=', $this->anio)
        ->where('tipo_movimiento', 'Gasto')
        ->get()
        ->toArray();
        // dd($this->cajas);
        // Inicializar nPagos con el conteo de pagarés de cada caja
        // Inicializar nPagos con el conteo de pagarés de cada caja
       foreach ($this->cajas as $index => $caja) {
        $this->nPagos[$index] = count($caja['pagares']);
    }

        // Si alguna caja no tiene pagares, crear uno por defecto
        // foreach ($this->cajas as $caja) {
        //     if (empty($caja['pagares'])) {
        //         $caja['pagares']->push(PagaresModel::create([
        //             'caja_id' => $caja['id'],
        //             'nPagos' => 1,
        //             'fecha_efecto' => now(),
        //             'nEfecto' => '0001',
        //             'banco_id' => 1, // Asigna un banco_id por defecto
        //             'estado' => 'pendiente',
        //             'importe_efecto' => $caja['importe']
        //         ]));
        //     }
        // }
    }


    public function updated($propertyName)
    {

        // dd($propertyName);

        // dd($propertyName);

        if(str_starts_with($propertyName, 'nPagos') || str_starts_with($propertyName, 'mes') || str_starts_with($propertyName, 'anio')){
            return;
        }

        list($otro, $cajaIndex, $otro2, $pagareIndex, $field) = explode('.', $propertyName);
    
        // Obtener el ID del pagaré desde el array
        $pagareId = $this->cajas[$cajaIndex]['pagares'][$pagareIndex]['id'];
    
        // Obtener el modelo de Pagares desde la base de datos
        $pagare = PagaresModel::find($pagareId);
    
        // Verificar si el modelo fue encontrado
        if ($pagare) {
            // Actualizar el campo del modelo
            $pagare->update([
                $field => $this->cajas[$cajaIndex]['pagares'][$pagareIndex][$field]
            ]);
        }
    }
    
    public function updatedMes()
    {
         // Obtener todas las cajas con sus pagares
         $this->cajas = Caja::with([
            'pagares',
            'proveedor',
            'facturasCompensadas.factura'
        ])
        ->whereIn('metodo_pago', ['Pagare', 'PAGARÉ', 'pagare', 'Pagaré', 'PAGARE'])
        ->whereMonth('fecha', '=', $this->mes)
        ->whereYear('fecha', '=', $this->anio)
        ->where('tipo_movimiento', 'Gasto')

        ->get()
        ->toArray();

        foreach ($this->cajas as $index => $caja) {
            $this->nPagos[$index] = count($caja['pagares']);
        }
    }

    public function updatedAnio()
    {
        $this->cajas = Caja::with([
            'pagares',
            'proveedor',
            'facturasCompensadas.factura'
        ])
        ->whereIn('metodo_pago', ['Pagare', 'PAGARÉ', 'pagare', 'Pagaré', 'PAGARE'])
        ->whereMonth('fecha', '=', $this->mes)
        ->whereYear('fecha', '=', $this->anio)
        ->where('tipo_movimiento', 'Gasto')

        ->get()
        ->toArray();

        foreach ($this->cajas as $index => $caja) {
            $this->nPagos[$index] = count($caja['pagares']);
        }
    }

    public function updatedNPagos($value, $key)
    {
        $caja = $this->cajas[$key];
        $currentPagosCount = count($caja['pagares']);

        if ($value > $currentPagosCount) {
            // Crear nuevos pagarés
            for ($i = $currentPagosCount; $i < $value; $i++) {
                PagaresModel::create([
                    'caja_id' => $caja['id'],
                    'nPagos' => $i + 1,
                    'fecha_efecto' => now(),
                    'nEfecto' => str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'banco_id' => 1, // Asigna un banco_id por defecto
                    'estado' => 'pendiente'
                ]);
            }
        } elseif ($value < $currentPagosCount) {
            // Eliminar pagarés sobrantes
            PagaresModel::where('caja_id', $caja['id'])->orderBy('nPagos', 'desc')->take($currentPagosCount - $value)->delete();
        }

        // Calcular el importe por pagaré

        if($value != 0){

            $importePorPagare = round($caja['total'] / $value, 2);

            $cajaModel = Caja::find($caja['id']);

            // Actualizar el importeEfecto de cada pagaré
            foreach ($cajaModel->pagares as $pagare) {
                if($pagare){
                    $pagare->update(['importe_efecto' => $importePorPagare]);
                }
            }
        }

        // Recargar las cajas
        $this->cajas = Caja::with([
            'pagares',
            'proveedor',
            'facturasCompensadas.factura'
        ])
        ->whereIn('metodo_pago', ['Pagare', 'PAGARÉ', 'pagare', 'Pagaré', 'PAGARE'])
        ->whereMonth('fecha', '=', $this->mes)
        ->whereYear('fecha', '=', $this->anio)
        ->where('tipo_movimiento', 'Gasto')

        ->get()
        ->toArray();
        
        foreach ($this->cajas as $index => $caja) {
            $this->nPagos[$index] = count($caja['pagares']);
        }
        // Emitir un evento para actualizar la vista
        $this->emit('refreshComponent');

        // Actualizar la propiedad nPagos
        //$this->nPagos[$key] = $value;
    }

    public function obtenerPagares()
    {
        return Caja::where(function($query) {
                $query->where('metodo_pago', 'PAGARE')
                      ->orWhere('metodo_pago', 'PAGARÉ')
                      ->orWhere('metodo_pago', 'pagare');
            })
            ->whereMonth('fecha', $this->mes)
            ->whereYear('fecha', $this->anio)
            ->get();
    }

    public function render()
    {
        return view('livewire.operaciones.pagares', [
            'cajas' => $this->cajas,
        ]);
    }
}
