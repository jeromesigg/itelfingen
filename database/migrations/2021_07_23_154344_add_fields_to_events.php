<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToEvents extends Migration
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
            $table->text('marketing_comment')->nullable();
            $table->integer('other_adults')->nullable();
            $table->integer('member_adults')->nullable();
            $table->integer('other_kids')->nullable();
            $table->integer('member_kids')->nullable();
            $table->integer('total_people')->nullable();
            $table->boolean('terms')->nullable();         
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
            $table->dropColumn('marketing_comment');
            $table->dropColumn('other_adults');
            $table->dropColumn('member_adults');
            $table->dropColumn('other_kids');
            $table->dropColumn('member_kids');
            $table->dropColumn('total_people');
            $table->dropColumn('terms');     
        });
    }
}
