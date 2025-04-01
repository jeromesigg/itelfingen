<?php

namespace App\Listeners;

use App\Models\Room;
use App\Events\EventInvoiceCreate;
use App\Models\Checkpoint;

class EventChecklistCreateListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EventInvoiceCreate $eventInvoice): void
    {
        //
        $event = $eventInvoice->event;
        if ($event->event_rooms->count() === 0) {
            $rooms = Room::where('archive_status_id', config('status.aktiv'))->orderBy('sort-index')->get();
            foreach ($rooms as $room) {
                $event_room = $event->event_rooms()->create([
                    'room_id' => $room->id,
                ]);
                foreach ($room->checkpoints()->get() as $checkpoint) {
                    $event_room->event_checkpoints()->create([
                        'checkpoint_id' => $checkpoint->id,
                    ]);
                }
            }
        }

    }
}
