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
        Schema::create('event_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('event_room_id')->index()->unsigned()->nullable();
            $table->foreign('event_room_id')->references('id')->on('event_rooms');
            $table->bigInteger('checkpoint_id')->index()->unsigned()->nullable();
            $table->foreign('checkpoint_id')->references('id')->on('checkpoints');
            $table->boolean('done')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_checkpoints');
    }
};
