<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\Incidencias;
use App\Models\PedidosIncidencias;

class RecordatorioIncidenciaMail extends Mailable
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
    public function __construct(User $empleado, $incidencia, $type)
    {
        $this->empleado = $empleado;
        $this->incidencia = $incidencia;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->type === 'normal' ? 'Recordatorio de Incidencia' : 'Recordatorio de Incidencia de Pedido';

        return $this->view('emails.recordatorio_incidencia')
            ->subject($subject)
            ->with([
                'empleado' => $this->empleado,
                'incidencia' => $this->incidencia,
                'type' => $this->type,
            ]);
    }
}
