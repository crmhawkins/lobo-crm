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
        //add column ninterno y nfactura
        Schema::table('caja', function (Blueprint $table) {
            if(!Schema::hasColumn('caja', 'ninterno'))
                $table->string('ninterno')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column ninterno y nfactura
        Schema::table('caja', function (Blueprint $table) {
            $table->dropColumn('ninterno');
        });
    }
};
