<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
            SeedPhoneQuery::class,
        ]);
        // $this->call(UserSeeder::class);
    }
}
