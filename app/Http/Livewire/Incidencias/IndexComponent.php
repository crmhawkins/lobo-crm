<?php

namespace App\Http\Livewire\Incidencias;

use App\Models\Incidencias;
use Livewire\Component;

class IndexComponent extends Component
{
    public $incidencias;
    public $estado = 'recibida';
    public $observaciones;
    public $editingIncidenciaId = null;


    public function mount()
    {
        $this->loadIncidencias();
    }

    public function loadIncidencias()
    {
        $this->incidencias = Incidencias::orderBy('estado')
            ->orderBy('created_at', 'desc')
            ->get();
    }
     // Función para cuando se llama a la alerta
     public function getListeners()
     {
         return [
                'refreshIncidencias' => '$refresh',
                'updateIncidenciaState'
         ];
     }

    public function editIncidencia($id)
    {
        $incidencia = Incidencias::find($id);
        if ($incidencia) {
            $this->editingIncidenciaId = $id;
            $this->observaciones = $incidencia->observaciones;
            $this->estado = $incidencia->estado;
        }
    }
    public function updateIncidenciaState($id, $newEstado)
    {
        $incidencia = Incidencias::find($id);
        if ($incidencia) {
            $incidencia->update(['estado' => $newEstado]);
            $this->loadIncidencias(); // Recargar incidencias
        }
    }

    public function updateIncidencia()
    {
        $incidencia = Incidencias::find($this->editingIncidenciaId);
        if ($incidencia) {
            $this->validate([
                'observaciones' => 'required|string',
                'estado' => 'required|in:recibida,tramite,solucionada,rechazada',
            ]);

            $incidencia->update([
                'observaciones' => $this->observaciones,
                'estado' => $this->estado,
            ]);

            $this->editingIncidenciaId = null; // Resetear el modo de edición
            $this->reset('observaciones', 'estado'); // Limpiar los campos del formulario
            $this->loadIncidencias(); // Recargar las incidencias
        }
    }
    public function createIncidencia()
    {
        // Validar los campos
        $this->validate([
            'observaciones' => 'required|string',
            'estado' => 'required|in:recibida,tramite,solucionada,rechazada',
        ]);

        // Crear una nueva incidencia
        Incidencias::create([
            'observaciones' => $this->observaciones,
            'estado' => $this->estado,
        ]);

        // Resetear los campos del formulario
        $this->reset('observaciones', 'estado');

        // Volver a cargar las incidencias
        $this->mount();

        // Cerrar el modal (usando JS o el evento Livewire emit)
        $this->dispatchBrowserEvent('close-modal');
    }

    public function deleteIncidencia($id)
    {
        $incidencia = Incidencias::find($id);
        if ($incidencia) {
            $incidencia->delete();
            $this->loadIncidencias(); // Recargar las incidencias
        }
    }
    public function cancelEdit()
    {
        $this->editingIncidenciaId = null; // Salir del modo de edición
        $this->reset('observaciones', 'estado'); // Limpiar los campos del formulario
    }
    public function render()
    {
        return view('livewire.incidencias.index-component', [
            'incidencias' => $this->incidencias,
        ]);
    }
}
