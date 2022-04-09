<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPictureAndSymbolToChapter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faq_chapters', function (Blueprint $table) {
            //
            $table->string('symbol');
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
        Schema::table('faq_chapters', function (Blueprint $table) {
            //
        });
    }
}
