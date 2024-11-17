<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Configuracion;

class AlertasMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $titulo;
    public $descripcion;

    public function __construct($user, $titulo, $descripcion)
    {
        $this->user = $user;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
    }

    public function build()
    {
        return $this->view('emails.alerta')
            ->subject('Alerta: ' . $this->titulo)
            ->with([
                'user' => $this->user,
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
            ]);
    }
}
