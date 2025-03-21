<?php

namespace App\Http\Livewire\Calendario;

use Livewire\Component;
use App\Models\Event;
use App\Models\User;
use App\Models\Alertas;
use Livewire\WithFileUploads; // Importa el trait para manejar archivos
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Mail;


class IndexComponent extends Component
{
    use LivewireAlert;

    use WithFileUploads; // Añade el trait para manejar archivos

    public $events = [];
    public $calendars = [];
    public $events2 = [];
    public $titulo;
    public $descripcion;
    public $imagen;
    public $usuariosSeleccionados = []; // Aquí se almacenan los usuarios seleccionados
    public $usuarios;
    public $userRole;

    public function getListeners()
    {
        return [
            'addItem',
            'updateItem',
            'delItem',
        ];
    }

    public function enviarAlerta()
    {
        $this->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen' => 'nullable|image|max:1024', // Validación para la imagen, si es opcional
            'usuariosSeleccionados' => 'required|array|min:1', // Asegurarse de que se seleccionen usuarios
        ]);

        // Subir la imagen si existe
        $imagePath = null;
        if ($this->imagen) {
            $imagePath = $this->imagen->store('alertas', 'public'); // Guardar la imagen en el storage público
        }

        // Enviar la alerta a los usuarios seleccionados
        foreach ($this->usuariosSeleccionados as $usuarioId) {
            Alertas::create([
                'user_id' => $usuarioId,
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'imagen' => $imagePath, // Guardar la ruta de la imagen si existe
                'leida' => false, // Marcar la alerta como no leída
                'popup' => true, // Marcar la alerta como una alerta emergente
            ]);
        }

        // Reiniciar los valores del formulario
        $this->reset(['titulo', 'descripcion', 'imagen', 'usuariosSeleccionados']);

        // Cerrar el modal y notificar que la alerta fue enviada
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('success', '¡Alerta enviada con éxito!');
        $this->alert('success', '¡Alerta enviada correctamente!', [
            'position' => 'center',
            'timer' => 1000,
            'toast' => false,
            'showConfirmButton' => false,
            'timerProgressBar' => true,
        ]);
    }

    public function mount()
    {
        $this->usuarios = User::all(); // Obtener todos los usuarios para el selector
        $this->userRole = auth()->user()->role; // Obtener el rol del usuario autenticado

        // Simulación de calendarios (usuarios)
        $this->calendars = [
            [
                'id' => '1',
                'name' => 'Eventos',
                'color' => '#ffffff',
                'borderColor' => '#ff0000',
                'backgroundColor' => '#ff0000',
                'dragBackgroundColor' => '#ff0000',
            ],
            [
                'id' => '2',
                'name' => 'Marketing',
                'color' => '#ffffff',
                'borderColor' => '#00ff00',
                'backgroundColor' => '#00ff00',
                'dragBackgroundColor' => '#00ff00',
            ],
        ];

        // Obtener los eventos desde la base de datos
        $this->events = Event::all()->map(function ($event) {
            return [
                'id' => $event->id,
                'calendarId' => $event->calendar_id, // Mapeo del campo calendar_id a calendarId
                'title' => $event->title,
                'location' => $event->location,
                'isPrivate' => $event->isPrivate,
                'isAllday' => $event->isAllDay,
                'state' => $event->state,
                'category' => $event->category,
                'start' => $event->start,  // Ya debe estar en el formato correcto
                'end' => $event->end,      // Ya debe estar en el formato correcto
            ];
        });
    }

    public function eliminarEvento($eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $event->delete();

            // Emitir un evento para notificar que el evento fue eliminado
            $this->alert('success', 'Evento eliminado correctamente', [
                'position' => 'center',
                'timer' => 1000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);

            // Redirigir a la misma página para recargarla
            return redirect()->to(request()->header('Referer'));
        } catch (\Exception $e) {
            $this->alert('error', 'Error al eliminar el evento', [
                'position' => 'center',
                'timer' => 1000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
        }
    }

    public function actualizarEvento($eventId, $title, $location)
    {
        if ($this->userRole != 1) {
            $this->alert('error', 'No tienes permiso para editar este evento', [
                'position' => 'center',
                'timer' => 1000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
            return;
        }

        try {
            $event = Event::findOrFail($eventId);
            $event->title = $title;
            $event->location = $location;
            $event->save();

            $this->alert('success', 'Evento actualizado correctamente', [
                'position' => 'center',
                'timer' => 1000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);

            return redirect()->to(request()->header('Referer'));
        } catch (\Exception $e) {
            $this->alert('error', 'Error al actualizar el evento', [
                'position' => 'center',
                'timer' => 1000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
        }
    }

    public function enviarEmail($userIds, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $users = User::whereIn('id', $userIds)->get();
        //dd($users);

        foreach ($users as $user) {
            Mail::to($user->email)->cc('Alejandro.martin@serlobo.com')->send(new \App\Mail\EventNotification($event));
        }

        $this->alert('success', 'Emails enviados correctamente', [
            'position' => 'center',
            'timer' => 1000,
            'toast' => false,
            'showConfirmButton' => false,
            'timerProgressBar' => true,
        ]);
    }

    public function render()
    {
        return view('livewire.calendario.index-component');
    }
}
