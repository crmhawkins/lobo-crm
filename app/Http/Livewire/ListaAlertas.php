<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Alertas;
use Illuminate\Support\Facades\Auth;

class ListaAlertas extends Component
{

    public $alertas;

    public $alertasTipo1;
    public $alertasTipo2;
    public $alertasTipo3;
    public $alertasTipo4;
    public $alertasTipo5;
    public $alertasTipo6;
    public $alertasTipo7;
    public $alertasTipo8;

    public function mount()
    {
        $this->alertas = Alertas::where('user_id', Auth::id())
            ->whereNull('leida') // Opcional: Cargar solo notificaciones no leídas
            ->get();
    }


    public function limpiarAlertas()
    {
        $this->alertas = Alertas::where('user_id', Auth::id())
            ->whereNull('leida') // Opcional: Cargar solo notificaciones no leídas
            ->get();

        foreach ($this->alertas as $alerta) {
            $alerta->leida = true;
            $alerta->save();
        }

        $this->alertas = Alertas::where('user_id', Auth::id())
            ->whereNull('leida') // Opcional: Cargar solo notificaciones no leídas
            ->get();
    }

    public function render()
    {
        return view('livewire.lista-alertas');
    }

    public function accion($stage, $alertaId, $referenciaId)
    {
        $alerta = Alertas::findOrFail($alertaId);
        $alerta->leida = true;
        $alerta->save();

        switch ($stage) {
            case 1:
                return redirect()->to('admin/clientes-edit/' . $referenciaId);
            case 2:
                return redirect()->to('admin/pedidos-edit/' . $referenciaId);
            case 3:
                $this->alertas = Alertas::where('user_id', Auth::id())
                    ->whereNull('leida') // Opcional: Cargar solo notificaciones no leídas
                    ->get();
                break;
            case 4:
                $this->alertas = Alertas::where('user_id', Auth::id())
                    ->whereNull('leida') // Opcional: Cargar solo notificaciones no leídas
                    ->get();
                break;
            case 5:
                $this->alertas = Alertas::where('user_id', Auth::id())
                    ->whereNull('leida') // Opcional: Cargar solo notificaciones no leídas
                    ->get();
                break;
            case 6:
                $this->alertas = Alertas::where('user_id', Auth::id())
                    ->whereNull('leida') // Opcional: Cargar solo notificaciones no leídas
                    ->get();
                break;
            case 7:
                return redirect()->to('admin/produccion-create');
            case 8:
                $this->alertas = Alertas::where('user_id', Auth::id())
                    ->whereNull('leida') // Opcional: Cargar solo notificaciones no leídas
                    ->get();
                break;
            default:
                $this->alertas = Alertas::where('user_id', Auth::id())
                    ->whereNull('leida') // Opcional: Cargar solo notificaciones no leídas
                    ->get();
                break;
        }
    }
}
