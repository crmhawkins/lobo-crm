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
        Schema::table('facturas', function (Blueprint $table) {
            //iva pero ya calculado
            $table->decimal('iva', 10, 2)->nullable();
            //total
            $table->decimal('total', 10, 2)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //dropColumn
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn('iva');
            $table->dropColumn('total');
        });
    }
};
