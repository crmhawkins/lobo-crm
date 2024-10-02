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
        Schema::table('incidencias', function (Blueprint $table) {
            $table->text('notas')->nullable()->after('observaciones');
        });
    
        Schema::table('pedidos_incidencias', function (Blueprint $table) {
            $table->text('notas')->nullable()->after('observaciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropColumn('notas');
        });
    
        Schema::table('pedidos_incidencias', function (Blueprint $table) {
            $table->dropColumn('notas');
        });
    }
};
