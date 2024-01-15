<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacturaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    public $datos;
    public $cliente;
    public $pedido;
    public $productos;


    public function __construct($pdf,$datos)
    {
        $this->pdf = $pdf;
        $this->datos = $datos;
        $this->cliente = $datos['cliente'];
        $this->pedido =$datos['pedido'];
        $this->productos =$datos['productos'];
    }

    public function build()
    {
        return $this->view('emails.factura')
                    ->subject('Su Pedido')
                    ->attachData($this->pdf, 'Factura.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with([
                        'cliente' => $this->cliente
                    ]);
    }
}
