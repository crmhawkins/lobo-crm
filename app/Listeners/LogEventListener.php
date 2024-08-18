<?php

namespace App\Listeners;

use App\Events\LogEvent;
use App\Models\LogActions;
use App\Models\Logs;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(LogEvent $event)
    {
        $descripcion_base = LogActions::where('id', $event->action_id)->first()->description;
        $descripcion = str_replace('{user}', User::where('id', $event->user->id)->first()->name, $descripcion_base);
        $descripcion = str_replace('{hora}', substr($event->fecha, 10, 9), $descripcion);
        $descripcion = str_replace('{referencia}', $event->reference, $descripcion);
        $descripcion = str_replace('{dia}', substr($event->fecha, 0, 10), $descripcion);
        $action = LogActions::where('id', $event->action_id)->first()->action;

        Logs::create([
            'user_id' => $event->user->id,
            'action' => $action,
            'description' => $descripcion,
            'date' => $event->fecha,
            'reference' => $event->reference
        ]);

    }
}
