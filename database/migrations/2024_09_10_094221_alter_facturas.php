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
        //gastos de transporte change to nullable
        Schema::table('facturas', function (Blueprint $table) {
            $table->decimal('gastos_transporte', 10, 2)->nullable()->change();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //change
        Schema::table('facturas', function (Blueprint $table) {
            $table->decimal('gastos_transporte', 10, 2)->change();
        });
    }
};
