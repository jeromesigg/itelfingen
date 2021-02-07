<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColorToEventStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_statuses', function (Blueprint $table) {
            //
            $table->string('color');
        });     
        DB::table('event_statuses')
        ->where('id', config('status.event_neu'))
        ->update([
            "color" => "P"
        ]);  
        DB::table('event_statuses')
        ->where('id', config('status.event_bestaetigt'))
        ->update([
            "color" => "B"
        ]);  
        DB::table('event_statuses')
        ->where('id', config('status.event_eigene'))
        ->update([
            "color" => "N"
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_statuses', function (Blueprint $table) {
            //
            $table->dropColumn('color');
        });
    }
}
