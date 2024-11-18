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
        //add column to table productos
        Schema::table('productos', function (Blueprint $table) {
            //add column is_pack
            $table->boolean('is_pack')->default(false)->after('unidades_por_caja');
            //add column products_id to table productos
            $table->json('products_id')->nullable()->after('is_pack');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column is_pack
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('is_pack');
            $table->dropColumn('products_id');
        });
    }
};
