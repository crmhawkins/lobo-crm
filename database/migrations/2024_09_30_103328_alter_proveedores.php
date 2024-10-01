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
        //add column to proveedores cuenta_contable_numero
        Schema::table('proveedores', function (Blueprint $table) {
            $table->string('cuenta_contable_numero')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column cuenta_contable_numero
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropColumn('cuenta_contable_numero');
        });
    }
};
