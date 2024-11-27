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
        //add column products_id_marketing
        Schema::table('productos', function (Blueprint $table) {
            //array de ids de productos
            $table->json('products_id_marketing')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column products_id_marketing
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('products_id_marketing');
        });
    }
};
