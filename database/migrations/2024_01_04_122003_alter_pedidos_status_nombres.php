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

            DB::table('pedidos_status')->where('id', 1)->update(['status' => 'Recibido']);
            DB::table('pedidos_status')->where('id', 2)->update(['status' => 'Aceptado en Almacén']);
            DB::table('pedidos_status')->where('id', 3)->update(['status' => 'Preparación']);
            DB::table('pedidos_status')->where('id', 4)->update(['status' => 'Albarán']);
            DB::table('pedidos_status')->where('id', 5)->update(['status' => 'Entregado']);
            DB::table('pedidos_status')->where('id', 6)->update(['status' => 'Facturado']);
            DB::table('pedidos_status')->where('id', 7)->update(['status' => 'Rechazado']);


            // Eliminar filas con id 7, 8 y 9
            DB::table('pedidos_status')->whereIn('id', [8, 9])->delete();
         //

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedidos_status', function (Blueprint $table) {
            //
        });
    }
};
