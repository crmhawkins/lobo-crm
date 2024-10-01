<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Caja;

class AsignarAsientosContables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asignar:asientos-contables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna números de asiento contable a todos los registros de la tabla caja, ordenados por created_at';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
         // Obtén todos los registros de la tabla 'caja' ordenados por 'created_at'
         $cajas = Caja::orderBy('created_at', 'asc')->get();

         // Inicializa el contador de asiento contable
         $contador = 1;
         $añoActual = date('Y'); // Obtiene el año actual
 
         // Itera sobre cada caja y asigna el número de asiento contable
         foreach ($cajas as $caja) {
             // Formatea el número a 6 dígitos con ceros a la izquierda
             $numeroAsiento = str_pad($contador, 6, '0', STR_PAD_LEFT) . '/' . $añoActual;
 
             // Asigna el número de asiento contable
             $caja->asientoContable = $numeroAsiento;
 
             // Guarda el registro con el número de asiento asignado
             $caja->save();
 
             // Incrementa el contador
             $contador++;
         }
 
         // Mensaje de éxito en la consola
         $this->info('Asientos contables asignados correctamente.');
 
         return 0;
     }
    
}
