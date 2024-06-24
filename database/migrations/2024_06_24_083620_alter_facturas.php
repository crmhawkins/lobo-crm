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
        //change column descripcion to text type
        Schema::table('facturas', function (Blueprint $table) {
            $table->text('descripcion')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //change column descripcion to string type
        Schema::table('facturas', function (Blueprint $table) {
            $table->string('descripcion')->change();
        });
    }
};
