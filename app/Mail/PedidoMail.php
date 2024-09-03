<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Configuracion;

class PedidoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    public $cliente;
    public  $pedido;
    public  $productos;
    public $configuracion;
    public $iva;

    public function __construct($pdf, $cliente,$pedido,$productos,$iva)
    {
        $this->pdf = $pdf;
        $this->cliente = $cliente;
        $this->pedido =$pedido;
        $this->productos =$productos;
        $this->iva =$iva;
        
        $this->configuracion = Configuracion::first();
    }

    public function build()
    {
        return $this->view('emails.pedido')
                    ->subject('Pedido nº '. $this->pedido->id . ' - ' . $this->cliente->nombre)
                    ->attachData($this->pdf, 'pedido.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with([
                        'cliente' => $this->cliente,
                        'configuracion' => $this->configuracion,
                    ]);
    }
}
