<?php

namespace App\Events;

use App\Models\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user instance.
     */
    public Event $event;

    public bool $oneDay;

    public array $positionArray;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Event $event, bool $one_day, array $position_array)
    {
        //
        $this->event = $event;
        $this->oneDay = $one_day;
        $this->positionArray = $position_array;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('event-created');
    }
}
