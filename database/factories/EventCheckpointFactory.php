<?php

namespace Database\Factories;

use App\Models\Checkpoint;
use App\Models\EventCheckpoint;
use App\Models\EventRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventCheckpoint>
 */
class EventCheckpointFactory extends Factory
{
    protected $model = EventCheckpoint::class;

    public function definition(): array
    {
        return [
            'event_room_id' => EventRoom::factory(),
            'checkpoint_id' => Checkpoint::factory(),
            'done' => false,
        ];
    }
}
