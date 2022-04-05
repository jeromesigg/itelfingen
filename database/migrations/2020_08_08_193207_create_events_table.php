<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('event_status_id')->index()->unsigned()->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('name');
            $table->string('firstname')->nullable();
            $table->string('group_name')->nullable();
            $table->string('email');
            $table->string('street');
            $table->integer('plz');
            $table->string('city');
            $table->string('telephone')->nullable();
            $table->text('comment')->nullable();
            $table->bigInteger('contract_status_id')->index()->unsigned()->nullable();
            $table->text('comment_intern')->nullable();
            $table->text('marketing_comment')->nullable();
            $table->boolean('terms')->nullable();
            $table->integer('total_amount')->nullable();
            $table->integer('total_days')->nullable();
            $table->integer('bexio_user_id')->nullable();
            $table->integer('bexio_invoice_id')->nullable();
            $table->integer('bexio_file_id')->nullable();
            $table->boolean('cleaning_mail')->default(false);
            $table->bigInteger('user_id')->index()->unsigned()->nullable();
            $table->integer('bexio_offer_id')->nullable();
            $table->integer('discount')->nullable();
            $table->boolean('last_info')->default(false);
            $table->integer('code')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('contract_status_id')->references('id')->on('contract_statuses');
            $table->foreign('event_status_id')->references('id')->on('event_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
