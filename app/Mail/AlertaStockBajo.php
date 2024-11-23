<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Mercaderia;

class AlertaStockBajo extends Mailable
{
    use Queueable, SerializesModels;

    public $mercaderia;

    /**
     * Create a new message instance.
     *
     * @param Mercaderia $mercaderia
     * @return void
     */
    public function __construct(Mercaderia $mercaderia)
    {
        $this->mercaderia = $mercaderia;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Alerta: Stock Bajo de Mercadería')
                    ->view('emails.alerta_stock_bajo')
                    ->with([
                        'nombreMercaderia' => $this->mercaderia->nombre,
                        'cantidadActual' => $this->mercaderia->cantidad,
                    ]);
    }
}