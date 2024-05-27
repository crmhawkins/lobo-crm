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
        //añadir columna qr
        Schema::table('mercaderia', function (Blueprint $table) {
            $table->string('qr')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //eliminar columna qr
        Schema::table('mercaderia', function (Blueprint $table) {
            $table->dropColumn('qr');
        });
    }
};
