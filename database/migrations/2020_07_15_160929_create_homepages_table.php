<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomepagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homepages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->bigInteger('main_photo_id')->index()->unsigned()->nullable();
            $table->bigInteger('background_top_photo_id')->index()->unsigned()->nullable();
            $table->bigInteger('background_bottom_photo_id')->index()->unsigned()->nullable();
            $table->bigInteger('big_login_photo_id')->index()->unsigned()->nullable();
            $table->bigInteger('small_login_photo_id')->index()->unsigned()->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('mail')->nullable();
            $table->text('postaddress')->nullable();

            $table->foreign('main_photo_id')->references('id')->on('photos')->onDelete('set Null');
            $table->foreign('background_top_photo_id')->references('id')->on('photos')->onDelete('set Null');
            $table->foreign('background_bottom_photo_id')->references('id')->on('photos')->onDelete('set Null');
            $table->foreign('big_login_photo_id')->references('id')->on('photos')->onDelete('set Null');
            $table->foreign('small_login_photo_id')->references('id')->on('photos')->onDelete('set Null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('homepages');
    }
}
