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
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            //create column calendarId
            $table->unsignedBigInteger('calendar_id')->nullable();
            //add column title
            $table->string('title');

            //add column location
            $table->string('location');

            //add column isPrivate
            $table->boolean('isPrivate');

            //add column isAllDay
            $table->boolean('isAllDay');

            //add column state
            $table->string('state');

            //add column category
            $table->string('category');

            //add column start
            $table->dateTime('start');
            
            //add column end
            $table->dateTime('end');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event');
    }
};
