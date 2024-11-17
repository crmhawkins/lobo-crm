<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function build()
    {
        return $this->view('emails.event-notification')
            ->subject('Notificación de Evento')
            ->with([
                'title' => $this->event->title,
                'location' => $this->event->location,
                'start' => $this->event->start,
                'end' => $this->event->end,
            ]);
    }
}
