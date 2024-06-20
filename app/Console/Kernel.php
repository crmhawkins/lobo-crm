<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Alertas;
use App\Models\Facturas;
use App\Models\User;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $tresDiasAtras = Carbon::now()->subDays(3)->startOfDay();
            $tresDias = Carbon::now()->addDays(3);
            $cincoDiasAtras = Carbon::now()->subDays(6)->startOfDay();
            $pedidosEnEnvio = Pedido::where('estado', 8)->where('updated_at', '<=', $cincoDiasAtras)->get();
            $FacturasVencimiento = Facturas::whereDate('fecha_vencimiento', '=', $tresDias->toDateString())->get();
            $pedidosEnPreparacion = Pedido::where('estado', 3)->where('updated_at', '<=', $tresDiasAtras)->get();

            //pedidos con fecha de salida, es decir que no sea null y que hayan pasado 3 dias desde la fecha de salida y aun no tenga fecha de entrega diferente a null
            $pedidosEnSalida = Pedido::whereNotNull('fecha_salida')->where('fecha_entrega', null)->whereDate('fecha_salida', '<=', $tresDias->toDateString())->get();
            //dd($pedidosEnSalida);

            foreach ($pedidosEnSalida as $pedido){
                //dd($pedido);
                //logger('pedidosEnSalida', $pedidosEnSalida);

                $alertaExistente = Alertas::where('referencia_id', $pedido->id)->where('stage', 8)->first();
                if (!$alertaExistente) {
                    Alertas::create([
                        'user_id' => 13,
                        'stage' => 8,
                        'titulo' => '',
                        'descripcion' => 'El pedido nº ' . $pedido->id . ' lleva más de 3 días sin fecha de entrega.',
                        'referencia_id' => $pedido->id,
                        'leida' => null,
                    ]);

                    //Enviar mensaje a director Comercial
                    $dGeneral = User::where('id', 13)->first();
                    $administrativo1 = User::where('id', 17)->first();
                    $administrativo2 = User::where('id', 18)->first();

                    $data = [['type' => 'text', 'text' => $pedido->id]];
                    $buttondata = [$pedido->id];

                    if(isset($dGeneral) && $dGeneral->telefono != null){
                        $phone = '+34'.$dGeneral->telefono;
                        enviarMensajeWhatsApp('automatico_noentregado', $data, $buttondata, $phone);
                    }

                    if(isset($administrativo1) && $administrativo1->telefono != null){
                        $phone = '+34'.$administrativo1->telefono;
                        enviarMensajeWhatsApp('automatico_noentregado', $data, $buttondata, $phone);
                    }

                    if(isset($administrativo2) && $administrativo2->telefono != null){
                        $phone = '+34'.$administrativo2->telefono;
                        enviarMensajeWhatsApp('automatico_noentregado', $data, $buttondata, $phone);
                    }

                   
                }


            }



            foreach ($pedidosEnPreparacion as $pedido) {
                // Verificar si ya existe una alerta para este pedido
                $alertaExistente = Alertas::where('referencia_id', $pedido->id)->where('stage', 4)->first();
                if (!$alertaExistente) {
                    Alertas::create([
                        'user_id' => 13,
                        'stage' => 4,
                        'titulo' => 'Pedido en preparación durante más de 2 días',
                        'descripcion' => 'El pedido nº ' . $pedido->id . ' lleva más de dos días en preparación.',
                        'referencia_id' => $pedido->id,
                        'leida' => null,
                    ]);

                    //Enviar mensaje a director Comercial
                    $dGeneral = User::where('id', 13)->first();
                    $administrativo1 = User::where('id', 17)->first();
                    $administrativo2 = User::where('id', 18)->first();

                    $data = [['type' => 'text', 'text' => $pedido->id]];
                    $buttondata = [$pedido->id];

                    if(isset($dGeneral) && $dGeneral->telefono != null){
                        $phone = '+34'.$dGeneral->telefono;
                        enviarMensajeWhatsApp('automatico_preparacion', $data, $buttondata, $phone);
                    }

                    if(isset($administrativo1) && $administrativo1->telefono != null){
                        $phone = '+34'.$administrativo1->telefono;
                        enviarMensajeWhatsApp('automatico_preparacion', $data, $buttondata, $phone);
                    }

                    if(isset($administrativo2) && $administrativo2->telefono != null){
                        $phone = '+34'.$administrativo2->telefono;
                        enviarMensajeWhatsApp('automatico_preparacion', $data, $buttondata, $phone);
                    }

                   
                }
            }

            foreach ($pedidosEnEnvio as $pedido) {
                // Verificar si ya existe una alerta para este pedido
                $alertaExistente = Alertas::where('referencia_id', $pedido->id)->where('stage', 5)->first();
                if (!$alertaExistente) {
                    Alertas::create([
                        'user_id' => 13,
                        'stage' => 5,
                        'titulo' => 'Pedido en envió más de 5 días',
                        'descripcion' => 'El pedido nº ' . $pedido->id . ' lleva más de 5 días en envió.',
                        'referencia_id' => $pedido->id,
                        'leida' => null,
                    ]);

                    $dGeneral = User::where('id', 13)->first();
                    $administrativo1 = User::where('id', 17)->first();
                    $administrativo2 = User::where('id', 18)->first();

                    $data = [['type' => 'text', 'text' => $pedido->id]];
                    $buttondata = [$pedido->id];
                 
                    if(isset($dGeneral) && $dGeneral->telefono != null){
                        $phone = '+34'.$dGeneral->telefono;
                        enviarMensajeWhatsApp('automatico_envio', $data, $buttondata, $phone);
                    }

                    if(isset($administrativo1) && $administrativo1->telefono != null){
                        $phone = '+34'.$administrativo1->telefono;
                        enviarMensajeWhatsApp('automatico_envio', $data, $buttondata, $phone);
                    }

                    if(isset($administrativo2) && $administrativo2->telefono != null){
                        $phone = '+34'.$administrativo2->telefono;
                        enviarMensajeWhatsApp('automatico_envio', $data, $buttondata, $phone);
                    }

                }

            }

            foreach ($FacturasVencimiento as $factura) {
                // Verificar si ya existe una alerta para este pedido
                $alertaExistente = Alertas::where('referencia_id', $factura->id)->where('stage',7)->first();

                if (!$alertaExistente) {
                    Alertas::create([
                        'user_id' => 13,
                        'stage' => 6,
                        'titulo' => 'Factura Vencimiento: En Tres Dias',
                        'descripcion' => 'La factura nº ' . $factura->numero_factura . ' vencera en 3 días.',
                        'referencia_id' => $factura->id,
                        'leida' => null,
                    ]);

                    $dGeneral = User::where('id', 13)->first();
                    $administrativo1 = User::where('id', 17)->first();
                    $administrativo2 = User::where('id', 18)->first();

                    $data = [['type' => 'text', 'text' => $factura->numero_factura]];
                    $buttondata = [$factura->id];

                    if(isset($dGeneral) && $dGeneral->telefono != null){
                        $phone = '+34'.$dGeneral->telefono;
                        enviarMensajeWhatsApp('automatico_vencimiento', $data, $buttondata, $phone);
                    }

                    if(isset($administrativo1) && $administrativo1->telefono != null){
                        $phone = '+34'.$administrativo1->telefono;
                        enviarMensajeWhatsApp('automatico_vencimiento', $data, $buttondata, $phone);
                    }

                    if(isset($administrativo2) && $administrativo2->telefono != null){
                        $phone = '+34'.$administrativo2->telefono;
                        enviarMensajeWhatsApp('automatico_vencimiento', $data, $buttondata, $phone);
                    }


                }
               
                    

            }
        })->everyMinute();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
