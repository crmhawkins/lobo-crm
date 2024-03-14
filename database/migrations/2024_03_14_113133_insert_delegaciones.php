<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;



return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Datos a insertar
        $delegaciones = [
            ['COD' => '00', 'nombre' => '00 GENERAL GLOBAL'],
            ['COD' => '01', 'nombre' => '01 ANDALUCIA'],
            ['COD' => '02', 'nombre' => '02 EXTREMADURA'],
            ['COD' => '03', 'nombre' => '03 MADRID'],
            ['COD' => '04', 'nombre' => '04 VALENCIA'],
            ['COD' => '05', 'nombre' => '05 MURCIA'],
            ['COD' => '06', 'nombre' => '06 CATALUÑA'],
            ['COD' => '07', 'nombre' => '07 CANARIAS'],
            ['COD' => '08', 'nombre' => '08 ASTURIAS'],
            ['COD' => '09', 'nombre' => '09 GALICIA'],
            ['COD' => '10', 'nombre' => '10 BALEARES'],
            ['COD' => '11', 'nombre' => '11 ZONA CENTRO'],
            ['COD' => '12', 'nombre' => '12 ARAGON'],
            ['COD' => '13', 'nombre' => '13 GIBRALTAR'],
            ['COD' => '14', 'nombre' => '14 CEUTA'],
            ['COD' => '15', 'nombre' => '15 MELILLA'],
            ['COD' => '16', 'nombre' => '16 GENERAL'],
        ];

        // Insertar los datos en la tabla delegaciones
        DB::table('delegaciones')->insert($delegaciones);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Opciones para revertir la migración, una opción podría ser eliminar las delegaciones específicas insertadas
        foreach (range(0, 16) as $cod) {
            DB::table('delegaciones')->where('COD', sprintf('%02d', $cod))->delete();
        }
    }
};
