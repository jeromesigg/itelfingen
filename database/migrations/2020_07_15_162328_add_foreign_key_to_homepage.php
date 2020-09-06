<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToHomepage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('homepages', function (Blueprint $table) {
            //
            $table->foreign('main_photo_id')->references('id')->on('photos')->onDelete('set Null');
            $table->foreign('background_top_photo_id')->references('id')->on('photos')->onDelete('set Null');
            $table->foreign('background_bottom_photo_id')->references('id')->on('photos')->onDelete('set Null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('homepages', function (Blueprint $table) {
            //
            $table->dropForeign(['main_photo_id']);
            $table->dropForeign(['background_top_photo_id']);
            $table->dropForeign(['background_bottom_photo_id']);
        });
    }
}
