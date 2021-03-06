<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchiveStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_statuses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
        });
        DB::table('archive_statuses')->insert( 
            array(
                ['id' => config('status.aktiv'),
                'name' => 'Aktiv'],
                ['id' => config('status.archiviert'),
                'name' => 'Archiviert'],
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
        Schema::dropIfExists('archive_statuses');
    }
}
