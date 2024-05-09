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
        Schema::table('caja', function (Blueprint $table) {
            //importeIva
            $table->decimal('importeIva', 10, 2)->nullable();
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
        
        //drop columns
        Schema::table('caja', function (Blueprint $table) {
            $table->dropColumn('importeIva');
            $table->dropColumn('total');
        });

    }
};
