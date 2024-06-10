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
        //add observaciones
        Schema::table('orden_produccion', function (Blueprint $table) {
            $table->text('observaciones')->nullable()->after('pedido_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop observaciones
        Schema::table('orden_produccion', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });
    }
};
