<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add pagado en caja
        Schema::table('caja', function (Blueprint $table) {
            //add pagado como cantidad pagada
            $table->decimal('pagado', 10, 2)->nullable();
            //add pendiente como cantidad pendiente
            $table->decimal('pendiente', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //add pagado en caja
        Schema::table('caja', function (Blueprint $table) {
            $table->dropColumn('pagado');
            $table->dropColumn('pendiente');
        });
    }
};
