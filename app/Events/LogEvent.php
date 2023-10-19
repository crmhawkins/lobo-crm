<?php

namespace App\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $action_id;
    public $reference;
    public $fecha;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $action_id, $reference)
    {
        $this->user = $user;
        $this->action_id = $action_id;
        $this->reference = $reference;
        $this->fecha = Carbon::now();
    }
}
