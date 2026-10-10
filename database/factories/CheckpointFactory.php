<?php

namespace Database\Factories;

use App\Models\Checkpoint;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Checkpoint>
 */
class CheckpointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence,
            'sort-index' => fake()->numberBetween(1, 100),
            'room_id' => Room::factory(),
            'archive_status_id' => 1,
        ];
    }
}
