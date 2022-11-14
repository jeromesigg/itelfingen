<?php

namespace App\Events;

use App\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user instance.
     *
     * @var \App\Models\Event
     */
    public Event $event;

    public bool $one_day;

    public array $position_array;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Event $event, bool $one_day, array $position_array)
    {
        //
        $this->event = $event;
        $this->one_day = $one_day;
        $this->position_array = $position_array;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('event-created');
    }
}
