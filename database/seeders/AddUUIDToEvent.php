<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddUUIDToEvent extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $events = \App\Models\Event::where('uuid', null)->get();
        foreach ($events as $event) {
            $event->uuid = \Illuminate\Support\Str::uuid();
            $event->save();
        }
    }
}
