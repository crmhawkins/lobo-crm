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
        //add importe
        Schema::table('pagares', function (Blueprint $table) {
            $table->decimal('importe_efecto', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop importe
        Schema::table('pagares', function (Blueprint $table) {
            $table->dropColumn('importe_efecto');
        });
    }
};
