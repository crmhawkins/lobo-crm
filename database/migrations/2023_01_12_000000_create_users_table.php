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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_department_id')->unsigned();
            // $table->bigInteger('user_position_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('role')->nullable();
            $table->string('username');
            $table->string('email')->unique();
            $table->string('image')->nullable();
            $table->string('seniority_years')->nullable();
            $table->string('seniority_months')->nullable();
            $table->string('holidays_days')->nullable();
            $table->tinyInteger('inactive');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_department_id')->references('id')->on('departamentos_users');
            // $table->foreign('user_position_id')->references('id')->on('positions');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
