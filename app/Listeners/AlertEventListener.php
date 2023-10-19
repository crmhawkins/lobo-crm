<?php

namespace App\Listeners;

use App\Events\AlertEvent;
use App\Models\Alerts;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AlertEventListener
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
     * @return void
     */
    public function handle(AlertEvent $evento)
    {
        Alerts::create([
            'user_id' => $evento->user_id,
            'stage_id' => $evento->stage_id,
            'status_id' => 0,
            'activation_date' => Carbon::now(),
            'reference' => $evento->reference
        ]);
    }
}
