<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pictures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->bigInteger('photo_id')->index()->unsigned()->nullable();
            $table->bigInteger('album_id')->index()->unsigned()->nullable();
        });
        Schema::table('pictures', function (Blueprint $table) {
            //
            $table->foreign('photo_id')->references('id')->on('photos');
            $table->foreign('album_id')->references('id')->on('albums');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pictures', function (Blueprint $table) {
            //
            $table->dropForeign(['photo_id']);
            $table->dropForeign(['album_id']);
        });
        Schema::dropIfExists('pictures');
    }
}
