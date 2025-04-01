<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_rooms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('room_id')->index()->unsigned()->nullable();
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->bigInteger('event_id')->index()->unsigned()->nullable();
            $table->foreign('event_id')->references('id')->on('events');
            $table->boolean('done')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_rooms');
    }
};
