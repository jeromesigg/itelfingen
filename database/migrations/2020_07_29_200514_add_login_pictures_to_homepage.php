<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoginPicturesToHomepage extends Migration
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
            $table->bigInteger('big_login_photo_id')->index()->unsigned()->nullable();
            $table->bigInteger('small_login_photo_id')->index()->unsigned()->nullable();
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
        Schema::table('homepages', function (Blueprint $table) {
            //
            $table->dropForeign(['big_login_photo_id']);
            $table->dropForeign(['small_login_photo_id']);
            $table->dropColumn('big_login_photo_id');
            $table->dropColumn('small_login_photo_id');
        });
    }
}
