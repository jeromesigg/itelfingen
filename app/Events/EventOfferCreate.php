<?php

namespace App\Events;

use App\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventOfferCreate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Event $event;

    public $additional_text;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        //
        $this->event = $event;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('event-offer-create');
    }
}
