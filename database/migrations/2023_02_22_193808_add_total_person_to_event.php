<?php

use App\Models\Position;
use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->integer('total_people')->nullable()->after('total_days');
        });
        DB::statement('UPDATE events set events.total_people = (SELECT sum(positions.amount) FROM positions where (positions.pricelist_position_id <5) and (positions.event_id = events.id)) where total_people is null;');
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
            $table->dropColumn('total_people');
        });
    }
};
