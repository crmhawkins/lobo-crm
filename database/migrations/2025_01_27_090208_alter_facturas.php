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
        //add column total_original
        Schema::table('facturas', function (Blueprint $table) {
            $table->decimal('total_original', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column total_original
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn('total_original');
        });
    }
};
