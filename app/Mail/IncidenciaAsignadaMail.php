<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IncidenciaAsignadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $empleado;
    public $incidencia;
    public $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($empleado, $incidencia, $type)
    {
        $this->empleado = $empleado;
        $this->incidencia = $incidencia;
        $this->type = $type; // Puede ser 'normal' o 'pedido'
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nueva incidencia asignada a su cargo')
                    ->view('emails.incidencia')
                    ->with([
                        'empleado' => $this->empleado,
                        'incidencia' => $this->incidencia,
                        'type' => $this->type,
                    ]);
    }
}
    