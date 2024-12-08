<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostesMarketingTable extends Migration
{
    public function up()
    {
        Schema::create('costes_marketing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('productos_marketing')->onDelete('cascade');
            $table->decimal('cost', 10, 2);
            $table->string('COD')->nullable();
            $table->year('year');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('costes_marketing');
    }
}