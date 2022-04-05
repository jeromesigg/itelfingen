<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('function')->nullable();
            $table->integer('sort-index');
            $table->bigInteger('archive_status_id')->index()->unsigned()->nullable();
            $table->foreign('archive_status_id')->references('id')->on('archive_statuses');
            $table->bigInteger('photo_id')->index()->unsigned()->nullable();
            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('set Null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
}
