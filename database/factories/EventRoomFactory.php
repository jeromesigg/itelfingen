<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventRoom;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventRoom>
 */
class EventRoomFactory extends Factory
{
    protected $model = EventRoom::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'room_id' => Room::factory(),
            'done' => false,
        ];
    }
}
