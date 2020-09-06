<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title')->nullable();
            $table->bigInteger('event_status_id')->index()->unsigned()->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('name');
            $table->string('firstname')->nullable();
            $table->string('group_name');
            $table->string('email');
            $table->string('street');
            $table->integer('plz');
            $table->string('city');
            $table->string('telephone');
            $table->text('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
