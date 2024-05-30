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
        //add column recargo
        Schema::table('facturas', function (Blueprint $table) {
            $table->decimal('recargo', 10, 2)->default(0);
            //total recargo
            $table->decimal('total_recargo', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column recargo
        Schema::table('facturas', function (Blueprint $table) {

            //if exist column recargo then drop
            if (Schema::hasColumn('facturas', 'recargo')) {
                $table->dropColumn('recargo');
            }

            //if exist column total_recargo then drop
            if (Schema::hasColumn('facturas', 'total_recargo')) {
                $table->dropColumn('total_recargo');
            }
        });
    }
};
