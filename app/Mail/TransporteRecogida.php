<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransporteRecogida extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    public $datos;
    public $cliente;
    public $pedido;
    public $productos;
    public $factura;
    public $productosFactura;
    public $num_albaran;
    public $almacen;


    public function __construct($pdf,$datos)
    {
        $this->pdf = $pdf;
        $this->datos = $datos;
        $this->cliente = $datos['cliente'];
        $this->pedido =$datos['pedido'];
        $this->productos =$datos['productos'];
        $this->factura = $datos['factura'];
        $this->productosFactura = $datos['productosFactura'];
        $this->num_albaran = $datos['num_albaran'];
        $this->almacen = $datos['almacen'];
        //dd($this->productosFactura);

    }

    public function build()
    {
        return $this->view('emails.transporterecogida')
                    ->subject('Albarán nº '. $this->num_albaran . ' - ' . $this->cliente->nombre . ' - ' . $this->cliente->localidad)
                    ->attachData($this->pdf, 'Albaran.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with([
                        'cliente' => $this->cliente
                    ]);
    }
}
