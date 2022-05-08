<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('salutation_id')->index()->unsigned()->nullable();
            $table->foreign('salutation_id')->references('id')->on('salutations');
            $table->string('name');
            $table->string('firstname')->nullable();
            $table->string('organisation')->nullable();
            $table->string('street');
            $table->integer('plz');
            $table->string('city');
            $table->string('email');
            $table->string('telephone')->nullable();
            $table->text('why');
            $table->text('comment')->nullable();
            $table->integer('bexio_user_id')->nullable();
            $table->integer('bexio_invoice_id')->nullable();
            $table->boolean('invoice_send')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
