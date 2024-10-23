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
        //add column order to productos
        Schema::table('productos', function (Blueprint $table) {
            $table->string('grupo')->nullable()->after('nombre');  // Columna para agrupar productos
            $table->integer('orden')->nullable()->after('grupo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['grupo', 'orden']);
        });
    }
};
