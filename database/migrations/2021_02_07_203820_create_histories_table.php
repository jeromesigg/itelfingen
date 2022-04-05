<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('shorttitle')->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description');
            $table->integer('sort-index');
            $table->bigInteger('archive_status_id')->index()->unsigned()->nullable();
            $table->bigInteger('photo_id')->index()->unsigned()->nullable();

            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('set Null');
            $table->foreign('archive_status_id')->references('id')->on('archive_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
}
