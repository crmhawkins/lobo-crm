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
        //alter table pedidos change type of observaciones to TEXT
        Schema::table('pedidos', function (Blueprint $table) {
            $table->text('observaciones')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //alter table pedidos change type of observaciones to VARCHAR
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('observaciones')->change();
        });
    }
};
