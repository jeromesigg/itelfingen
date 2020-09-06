<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('is_admin')->default(0);
            $table->integer('is_team')->default(0);
            $table->timestamps();
        });
        // Insert some stuff
        DB::table('roles')->insert(
           array(
               ['id' => config('role_Administrator'), 'name' => 'Administrator', 'is_admin' => true, 'is_team' => false],
               ['id' => config('role_Team'), 'name' => 'Team', 'is_admin' => false, 'is_team' => true],
               ['id' => config('role_Gast'), 'name' => 'Gast', 'is_admin' => false, 'is_team' => false],
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
        Schema::dropIfExists('roles');
    }
}
