<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CambioEstadoIncidenciaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $empleado;
    public $incidencia;
    public $type;

    public function __construct($empleado, $incidencia, $type)
    {
        $this->empleado = $empleado;
        $this->incidencia = $incidencia;
        $this->type = $type; // Puede ser 'normal' o 'pedido'
    }

    public function build()
    {
        return $this->view('emails.incidenciaestado')
                    ->subject('Cambio de Estado de Incidencia')
                    ->with([
                        'empleado' => $this->empleado,
                        'incidencia' => $this->incidencia,
                        'type' => $this->type,
                    ]);
    }
}
