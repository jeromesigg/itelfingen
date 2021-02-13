<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_statuses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
        });
        DB::table('contract_statuses')->insert( 
            array(
                ['id' => config('status.contract_offen'),
                'name' => 'Offen'],
                ['id' => config('status.contract_versendet'),
                'name' => 'Versendet'],
                ['id' => config('status.contract_zurück'),
                'name' => 'Erhalten'],
                ['id' => config('status.contract_storniert'),
                'name' => 'Storniert'],
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_statuses');
    }
}
