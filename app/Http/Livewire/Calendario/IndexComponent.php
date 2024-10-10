<?php

namespace App\Http\Livewire\Calendario;

use Livewire\Component;
use App\Models\Event;

class IndexComponent extends Component
{
    public $events = [];
    public $calendars = [];
    public $events2 = [];

    public function getListeners()
    {
        return [
            'addItem',
            'updateItem',
            'delItem',
        ];
    }

    public function mount()
{
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



    public function render()
    {
        return view('livewire.calendario.index-component');
    }
}
