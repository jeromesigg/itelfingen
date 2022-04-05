<?php

use Database\Seeders\BasisdatenSeeder;
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
        ]);
        // $this->call(UserSeeder::class);
    }
}
