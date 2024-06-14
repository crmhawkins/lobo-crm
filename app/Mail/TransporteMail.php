<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransporteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    public $datos;
    public $cliente;
    public $pedido;
    public $productos;
    public $observaciones;
    public $almacen;


    public function __construct($pdf,$datos, $observaciones)
    {
        $this->pdf = $pdf;
        $this->datos = $datos;
        $this->cliente = $datos['cliente'];
        $this->pedido =$datos['pedido'];
        $this->productos =$datos['productos'];
        $this->observaciones = $observaciones;
        $this->almacen = $datos['almacen'];
    }

    public function build()
    {
        return $this->view('emails.transporte')
                    ->subject('Albaran')
                    ->attachData($this->pdf, 'Albaran.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with([
                        'cliente' => $this->cliente
                    ]);
    }
}
