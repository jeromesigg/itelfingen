<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AddUUIDToEvent extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $events = Event::where('uuid', null)->get();
        foreach ($events as $event) {
            $event->uuid = Str::uuid();
            $event->save();
        }
    }
}
