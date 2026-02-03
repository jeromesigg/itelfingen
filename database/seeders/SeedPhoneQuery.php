<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeedPhoneQuery extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $events = \App\Models\Event::where('phonenumber_query', null)->get();
        foreach ($events as $event) {
            $event->phonenumber_query = str_replace(' ', '', $event->telephone);
            $event->phonenumber_query = str_replace('-', '', $event->phonenumber_query);
            $event->save();
        }
    }
}
