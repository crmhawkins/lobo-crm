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
use App\Models\StockMercaderiaEntrante;
use App\Models\MercaderiaProduccion;
use App\Models\Mercaderia;
use App\Models\Retencion;

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
            // $mercaderiaCantidad = StockMercaderiaEntrante::All();
            // $mercaderiaProduccion = MercaderiaProduccion::All();
            $mercaderias = Mercaderia::all();
            //pedidos con fecha de salida, es decir que no sea null y que hayan pasado 3 dias desde la fecha de salida y aun no tenga fecha de entrega diferente a null
            $pedidosEnSalida = Pedido::whereNotNull('fecha_salida')->where('fecha_entrega', null)->whereDate('fecha_salida', '<=', $tresDias->toDateString())->get();
            //dd($pedidosEnSalida);

            foreach ($mercaderias as $mercaderia){
                
                //coger la cantidad y en produccion
                $cantidad = StockMercaderiaEntrante::where('mercaderia_id', $mercaderia->id)->get()->sum('cantidad');
                $produccion = MercaderiaProduccion::where('mercaderia_id', $mercaderia->id)->get()->sum('cantidad');


                //si la cantidad es un 20% menor a la produccion se envia una alerta

                if($cantidad < ($produccion * 0.2)){
                    
                    $alertaExistente = Alertas::where('referencia_id', $mercaderia->id)->where('stage', 9)->first();
                    
                    if (!$alertaExistente) {
                        Alertas::create([
                            'user_id' => 13,
                            'stage' => 9,
                            'titulo' => 'Stock de Mercaderia Bajo',
                            'descripcion' => 'La mercaderia ' . $mercaderia->nombre . ' tiene un stock bajo.',
                            'referencia_id' => $mercaderia->id,
                            'leida' => null,
                        ]);

                        // //Enviar mensaje a director Comercial
                        // $dGeneral = User::where('id', 13)->first();
                        // $administrativo1 = User::where('id', 17)->first();
                        // $administrativo2 = User::where('id', 18)->first();

                        // $data = [['type' => 'text', 'text' => $mercaderia->nombre]];
                        // $buttondata = [$mercaderia->id];
                    }
                }


            }

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


            $retenciones = Retencion::orderBy('dias_retencion', 'desc')->get();

        foreach ($retenciones as $retencion) {
            // Obtener las facturas vencidas que cumplen con los días de retención
            $facturasVencidas = Facturas::whereNull('tipo')
                ->where('estado', 'Pendiente')
                ->where('fecha_vencimiento', '<', Carbon::now()->subDays($retencion->dias_retencion))
                ->where(function ($query) use ($retencion) { // Asegúrate de pasar $retencion al closure
                    $query->whereNull('retencion_id')
                          ->orWhere('retencion_id', '<', $retencion->id);
                })
                ->get();

            foreach ($facturasVencidas as $factura) {
                // Si total_original es null, copiar el total actual
                if($factura->cliente && $factura->cliente->delegacion){
                    $delegacion = $factura->cliente->delegacion->nombre;

                    $valorBase = ($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA' || $delegacion == '01.1 ESTE – SUR EXTERIOR' || $delegacion == '08 OESTE - INSULAR') 
                    ? $factura->precio 
                    : $factura->total;
        

                    if (is_null($factura->total_original)) {
                        $factura->total_original = $valorBase;
                    }

                    // Calcular el nuevo total aplicando la retención
                    $nuevoTotal = $factura->total_original + ($factura->total_original * $retencion->porcentaje / 100);
                    $valorBase = ($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA' || $delegacion == '01.1 ESTE – SUR EXTERIOR' || $delegacion == '08 OESTE - INSULAR') 
                    ? $factura->precio = $nuevoTotal
                    : $factura->total = $nuevoTotal;
                    $factura->retencion_id = $retencion->id;
                    $factura->save();
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
