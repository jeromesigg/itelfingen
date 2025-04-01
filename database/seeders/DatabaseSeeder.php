<?php

use Illuminate\Database\Seeder;
use Database\Seeders\AddUUIDToEvent;
use Database\Seeders\BasisdatenSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            BasisdatenSeeder::class,
            AddUUIDToEvent::class,
        ]);
        // $this->call(UserSeeder::class);
    }
}
