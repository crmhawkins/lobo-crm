<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Configuracion;

class RecordatorioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    public $datos;
    public $cliente;
    public $pedido;
    public $productos;
    public $factura;
    public $tipo;
    public $subject;
    public $configuracion;


    public function __construct($pdf,$datos)
    {
        $this->pdf = $pdf;
        $this->datos = $datos;
        $this->cliente = $datos['cliente'];
        $this->pedido =$datos['pedido'];
        $this->productos =$datos['productos'];
        $this->factura = $datos['factura'];
        $this->tipo = $datos['tipo'];
        if($this->tipo == 'impago'){
            $this->subject = 'Recordatorio de Pago de Factura Vencida';
        }else{
            $this->subject = 'Aviso de Vencimiento de Factura';
        }
        $this->configuracion = Configuracion::first();
    }

    public function build()
    {
        return $this->view('emails.recordatorio')
                    ->subject($this->subject)
                    ->attachData($this->pdf, 'Factura.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with([
                        'cliente' => $this->cliente,
                        'configuracion' => $this->configuracion,
                    ]);
    }
}
