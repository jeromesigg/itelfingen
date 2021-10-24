<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->text('description');
            $table->bigInteger('faq_chapter_id')->index()->unsigned()->nullable();
            $table->foreign('faq_chapter_id')->references('id')->on('faq_chapters');
            $table->bigInteger('photo_id')->index()->unsigned()->nullable();
            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('set Null');
            $table->integer('sort-index');
            $table->bigInteger('archive_status_id')->index()->unsigned()->nullable();
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
        Schema::dropIfExists('faqs');
    }
}
