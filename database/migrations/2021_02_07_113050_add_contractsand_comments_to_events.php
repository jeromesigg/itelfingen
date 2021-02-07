<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractsandCommentsToEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            //
            $table->bigInteger('contract_status_id')->index()->unsigned()->nullable();
            $table->foreign('contract_status_id')->references('id')->on('contract_statuses');
            $table->text('comment_intern')->nullable();
            $table->string('contract')->nullable();
            $table->string('contract_signed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            //
            $table->dropForeign(['contract_status_id']);
            $table->dropColumn('contract_status_id');
            $table->dropColumn('comment_intern');
            $table->dropColumn('contract');
            $table->dropColumn('contract_signed');
        });
    }
}
