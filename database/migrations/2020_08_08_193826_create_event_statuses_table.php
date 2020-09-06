<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_statuses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
        });
        DB::table('event_statuses')->insert( 
            array(
                ['id' => config('status.event_neu'),
                'name' => 'Neu'],
                ['id' => config('status.event_bestaetigt'),
                'name' => 'Bestätigt'],
                ['id' => config('status.event_storniert'),
                'name' => 'Storniert'],
                ['id' => config('status.event_eigene'),
                'name' => 'Eigene Termine'],
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_statuses');
    }
}
