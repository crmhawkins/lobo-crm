<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Alertas;
use App\Models\Facturas;

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
