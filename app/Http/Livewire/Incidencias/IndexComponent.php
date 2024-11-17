<?php

namespace App\Http\Livewire\Incidencias;

use App\Models\Incidencias;
use App\Models\PedidosIncidencias;
use App\Models\Pedido;
use App\Models\User;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use Illuminate\Support\Facades\Auth;
use App\Models\Alertas;
use App\Mail\IncidenciaAsignadaMail;
use App\Mail\CambioEstadoIncidenciaMail;
use App\Mail\RecordatorioIncidenciaMail;

use Illuminate\Support\Facades\Mail;

class IndexComponent extends Component
{
    use LivewireAlert;

    public $incidencias;
    public $pedidosIncidencias;
    public $estado = 'recibida';
    public $observaciones;
    public $editingIncidenciaId = null;
    public $editingPedidoIncidenciaId = null;
    public $pedidos;
    public $pedido_id;
    public $factura_id;
    public $users;
    public $user_id;
    public $activeTab = 'normales';  // Nueva propiedad para la pestaña activa
    public $notas; // Nuevo campo para notas

    // Método para cambiar la pestaña activa
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
    public function mount()
    {
        $this->users = User::orderBy('name')->get();
        $this->pedidos = Pedido::all();
        $this->loadIncidencias();
    }

    public function loadIncidencias()
    {
        if (Auth::user()->isAdmin()) {
            // Cargar todas las incidencias normales
            $this->incidencias = Incidencias::orderBy('estado')
                ->orderBy('created_at', 'desc')
                ->get();

            // Cargar todas las incidencias de pedidos
            $this->pedidosIncidencias = PedidosIncidencias::orderBy('estado')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            //cargar solo las del usuario que esta viendo la vista
            $user = Auth::user();

            // Cargar todas las incidencias normales
            $this->incidencias = Incidencias::where('user_id', $user->id)
                ->orderBy('estado')
                ->orderBy('created_at', 'desc')
                ->get();

            // Cargar todas las incidencias de pedidos
            $this->pedidosIncidencias = PedidosIncidencias::where('user_id', $user->id)
                ->orderBy('estado')
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    public function getListeners()
    {
        return [
            'refreshIncidencias' => '$refresh',
            'updateIncidenciaState',        // Para las incidencias normales
            'updatePedidoIncidenciaState',  // Para las incidencias de pedidos
        ];
    }

    // Editar Incidencia Normal
    public function editIncidencia($id)
    {
        $incidencia = Incidencias::find($id);
        if ($incidencia) {
            $this->editingIncidenciaId = $id;
            $this->user_id = $incidencia->user_id;
            $this->observaciones = $incidencia->observaciones;
            $this->estado = $incidencia->estado;
        }
    }
    public function resetForm()
    {
        $this->reset(['user_id', 'observaciones', 'estado', 'pedido_id', 'factura_id']);
    }


    // Editar Incidencia de Pedido
    public function editPedidoIncidencia($id)
    {
        $pedidoIncidencia = PedidosIncidencias::find($id);
        if ($pedidoIncidencia) {
            $this->editingPedidoIncidenciaId = $id;
            $this->user_id = $pedidoIncidencia->user_id;
            $this->pedido_id = $pedidoIncidencia->pedido_id;

            $this->observaciones = $pedidoIncidencia->observaciones;
            $this->estado = $pedidoIncidencia->estado;
        }
    }

    // Actualizar Estado de Incidencias Normales (Drag-and-Drop)
    public function updateIncidenciaState($id, $newEstado)
    {
        $incidencia = Incidencias::find($id);
        if ($incidencia) {
            $incidencia->update(['estado' => $newEstado]);

            try {

                $empleado = User::find($incidencia->user_id);
                Mail::to($empleado->email)
                    ->bcc('Alejandro.martin@serlobo.com')  // Aquí colocas el email que recibirá la copia oculta (BCC)
                    ->send(new CambioEstadoIncidenciaMail($empleado, $incidencia, 'normal'));

                // Mail::to('ivan.mayol@hawkins.es')
                // ->bcc('ivan.mayol@hawkins.es')  // Aquí colocas el email que recibirá la copia oculta (BCC)
                // ->send(new CambioEstadoIncidenciaMail($empleado, $incidencia , 'normal'));

                Alertas::create([
                    'user_id' => 13,
                    'stage' => 9,
                    'titulo' => 'Cambio de estado de incidencia',
                    'descripcion' => 'Se ha cambiado el estado de la incidencia',
                    'referencia_id' => $incidencia->id,
                    'leida' => null,
                ]);
            } catch (\Exception $e) {
                //dd($e);
            }



            $this->loadIncidencias();
        }
    }

    // Actualizar Estado de Incidencias de Pedidos (Drag-and-Drop)
    public function updatePedidoIncidenciaState($id, $newEstado)
    {
        $pedidoIncidencia = PedidosIncidencias::find($id);
        if ($pedidoIncidencia) {
            $pedidoIncidencia->update(['estado' => $newEstado]);

            try {

                $empleado = User::find($pedidoIncidencia->user_id);
                Mail::to($empleado->email)
                    ->bcc('Alejandro.martin@serlobo.com')  // Aquí colocas el email que recibirá la copia oculta (BCC)
                    ->send(new CambioEstadoIncidenciaMail($empleado, $incidencia, 'pedido'));

                // Mail::to('ivan.mayol@hawkins.es')
                // ->bcc('ivan.mayol@hawkins.es')  // Aquí colocas el email que recibirá la copia oculta (BCC)
                // ->send(new CambioEstadoIncidenciaMail($empleado, $pedidoIncidencia, 'pedido'));

                Alertas::create([
                    'user_id' => 13,
                    'stage' => 9,
                    'titulo' => 'Cambio de estado de incidencia',
                    'descripcion' => 'Se ha cambiado el estado de la incidencia',
                    'referencia_id' => $pedidoIncidencia->id,
                    'leida' => null,
                ]);
            } catch (\Exception $e) {
            }

            $this->loadIncidencias();
        }
    }

    // Crear Incidencia Normal
    public function createIncidencia()
    {

        if (!Auth::user()->isAdmin() && !Auth::user()->role == '7') {
            //alert para usuarios que no son admin
            $this->alert('warning', 'No tienes permisos para crear incidencias', [
                'position' =>  'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
            return;
        }


        $this->validate([
            'user_id' => 'required',
            'observaciones' => 'required|string',
            'estado' => 'required|in:recibida,tramite,solucionada,rechazada',
        ]);

        $incidencia = Incidencias::create([
            'user_id' => $this->user_id,
            'observaciones' => $this->observaciones,
            'estado' => $this->estado,
        ]);

        // Obtener el empleado asignado
        $empleado = User::find($this->user_id);
        Mail::to($empleado->email)
            ->bcc('Alejandro.martin@serlobo.com')  // Aquí colocas el email que recibirá la copia oculta (BCC)
            ->send(new IncidenciaAsignadaMail($empleado, $incidencia, 'normal'));

        // Mail::to('ivan.mayol@hawkins.es')
        // ->bcc('ivan.mayol@hawkins.es')  // Aquí colocas el email que recibirá la copia oculta (BCC)
        // ->send(new IncidenciaAsignadaMail($empleado, $incidencia, 'normal'));


        try {
            Alertas::create([
                'user_id' => 13,
                'stage' => 9,
                'titulo' => 'Incidencia Creada',
                'descripcion' => 'Tiene una incidencia nueva a cargo del usuario ' . $incidencia->user->name . ' ' . $incidencia->user->surname . ' a fecha ' . $incidencia->created_at,
                'referencia_id' => $incidencia->id,
                'leida' => null,
            ]);

            Alertas::create([
                'user_id' => $this->user_id,
                'stage' => 9,
                'titulo' => 'Incidencia Creada',
                'descripcion' => 'Tiene una incidencia nueva a su cargo ' . $incidencia->user->name . ' ' . $incidencia->user->surname . ' a fecha ' . $incidencia->created_at,
                'referencia_id' => $incidencia->id,
                'leida' => null,
            ]);
        } catch (\Exception $e) {
            //dd($e);
        }


        $this->reset('observaciones', 'estado');
        $this->loadIncidencias();
        $this->dispatchBrowserEvent('close-modal');
    }

    public function recordatorioIncidencia($id, $type = 'normal')
    {
        if ($type === 'normal') {
            $incidencia = Incidencias::find($id);
        } else {
            $incidencia = PedidosIncidencias::find($id);
        }

        if ($incidencia) {
            $empleado = User::find($incidencia->user_id);
            try {
                Mail::to($empleado->email)
                    ->bcc('Alejandro.martin@serlobo.com')  // Aquí colocas el email que recibirá la copia oculta (BCC)
                    ->send(new RecordatorioIncidenciaMail($empleado, $incidencia, $type));

                $this->alert('success', 'Recordatorio enviado correctamente', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                    'showConfirmButton' => false,
                    'timerProgressBar' => true,
                ]);
            } catch (\Exception $e) {
                $this->alert('error', 'Error al enviar el recordatorio', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                    'showConfirmButton' => false,
                    'timerProgressBar' => true,
                ]);
            }
        }
    }

    // Crear Incidencia de Pedido
    public function createIncidenciaPedido()
    {


        if (!Auth::user()->isAdmin() && !Auth::user()->role == '7') {            //alert para usuarios que no son admin
            $this->alert('warning', 'No tienes permisos para crear incidencias', [
                'position' =>  'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
            return;
        }

        $this->validate([
            'user_id' => 'required',
            'pedido_id' => 'required',
            'factura_id' => 'nullable',
            'observaciones' => 'required|string',
            'estado' => 'required|in:recibida,tramite,solucionada,rechazada',
        ]);

        $incidencia =  PedidosIncidencias::create([
            'user_id' => $this->user_id,
            'pedido_id' => $this->pedido_id,
            'factura_id' => $this->factura_id,
            'observaciones' => $this->observaciones,
            'estado' => $this->estado,
        ]);

        $empleado = User::find($this->user_id);
        Mail::to($empleado->email)
            ->bcc('Alejandro.martin@serlobo.com')  // Aquí colocas el email que recibirá la copia oculta (BCC)
            ->send(new IncidenciaAsignadaMail($empleado, $incidencia, 'pedido'));

        // Mail::to('ivan.mayol@hawkins.es')
        // ->bcc('ivan.mayol@hawkins.es')  // Aquí colocas el email que recibirá la copia oculta (BCC)
        // ->send(new IncidenciaAsignadaMail($empleado, $incidencia, 'pedido'));

        try {
            Alertas::create([
                'user_id' => 13,
                'stage' => 9,
                'titulo' => 'Incidencia de Pedido Creada a cargo del usuario ' . $incidencia->user->name . ' ' . $incidencia->user->surname . ' a fecha ' . $incidencia->created_at,
                'descripcion' => 'Tiene una incidencia de Pedido nueva',
                'referencia_id' => $incidencia->id,
                'leida' => null,
            ]);

            Alertas::create([
                'user_id' => $this->user_id,
                'stage' => 9,
                'titulo' => 'Incidencia de Pedido Creada',
                'descripcion' => 'Tiene una incidencia de Pedido nueva a su cargo ' . $incidencia->user->name . ' ' . $incidencia->user->surname . ' a fecha ' . $incidencia->created_at,
                'referencia_id' => $incidencia->id,
                'leida' => null,
            ]);
        } catch (\Exception $e) {
            //dd($e);
        }

        $this->reset('observaciones', 'estado', 'pedido_id', 'factura_id');
        $this->loadIncidencias();
        $this->dispatchBrowserEvent('close-modal');
    }



    public function editNotas($id, $type = 'normal')
    {
        if ($type === 'normal') {
            $incidencia = Incidencias::find($id);
        } else {
            $incidencia = PedidosIncidencias::find($id);
        }

        if ($incidencia && Auth::user()->id === $incidencia->user_id) {
            $this->editingIncidenciaId = $id;
            $this->notas = $incidencia->notas;
        }
    }

    public function updateNotas($type = 'normal')
    {
        $this->validate([
            'notas' => 'required|string',
        ]);
        if ($type === 'normal') {
            $incidencia = Incidencias::find($this->editingIncidenciaId);
        } else {
            $incidencia = PedidosIncidencias::find($this->editingIncidenciaId);
        }

        if ($incidencia && Auth::user()->id === $incidencia->user_id) {
            $incidencia->update([
                'notas' => $this->notas,
            ]);

            $this->reset('notas');
            $this->loadIncidencias();
        }
    }

    // Actualizar Incidencia Normal
    public function updateIncidencia()
    {
        $incidencia = Incidencias::find($this->editingIncidenciaId);
        if ($incidencia) {
            $this->validate([
                'user_id' => 'required',
                'observaciones' => 'required|string',
                'estado' => 'required|in:recibida,tramite,solucionada,rechazada',
            ]);

            $incidencia->update([
                'user_id' => $this->user_id,
                'observaciones' => $this->observaciones,
                'estado' => $this->estado,
            ]);
            $this->resetForm();
            $this->loadIncidencias();
        }
    }

    // Actualizar Incidencia de Pedido
    public function updatePedidoIncidencia()
    {
        $pedidoIncidencia = PedidosIncidencias::find($this->editingPedidoIncidenciaId);
        if ($pedidoIncidencia) {
            $this->validate([
                'user_id' => 'required',
                'pedido_id' => 'required',
                'observaciones' => 'required|string',
                'estado' => 'required|in:recibida,tramite,solucionada,rechazada',
            ]);

            $pedidoIncidencia->update([
                'user_id' => $this->user_id,
                'pedido_id' => $this->pedido_id,
                'observaciones' => $this->observaciones,
                'estado' => $this->estado,
            ]);

            $this->resetForm();

            $this->loadIncidencias();

            //livewire alert pero sin confirming
            $this->alert('success', '¡Incidencia actualizada correctamente!', [
                'position' => 'center',
                'timer' => 1000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
        }
    }

    // Eliminar Incidencia Normal
    public function deleteIncidencia($id)
    {
        $incidencia = Incidencias::find($id);
        if ($incidencia) {
            $incidencia->delete();
            $this->loadIncidencias();
        }
    }

    // Eliminar Incidencia de Pedido
    public function deletePedidoIncidencia($id)
    {
        $pedidoIncidencia = PedidosIncidencias::find($id);
        if ($pedidoIncidencia) {
            $pedidoIncidencia->delete();
            $this->loadIncidencias();
        }
    }

    public function cancelEdit()
    {
        $this->editingIncidenciaId = null;
        $this->editingPedidoIncidenciaId = null;
        $this->reset('observaciones', 'estado');
    }

    public function render()
    {
        return view('livewire.incidencias.index-component');
    }
}
