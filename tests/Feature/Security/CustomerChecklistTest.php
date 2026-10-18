<?php

namespace Tests\Feature\Security;

use App\Models\Checkpoint;
use App\Models\EventCheckpoint;
use App\Models\EventRoom;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Checkliste im Kundenbereich: Gäste haken Punkte ihrer eigenen Buchung ab (ohne Login, über die Buchungs-UUID).
 */
class CustomerChecklistTest extends TestCase
{
    public function test_checklist_page_renders_for_booking_uuid(): void
    {
        $s = BookingScenario::create();

        $this->get("/bookings/{$s->confirmedEvent->uuid}/checklist")
            ->assertOk()
            ->assertSee('Herd reinigen')
            ->assertSee(route('bookings.checkpointDone', $s->confirmedEvent->uuid), false);
    }

    public function test_guest_toggles_checkpoint_of_own_booking(): void
    {
        $s = BookingScenario::create();

        $this->postJson("/bookings/{$s->confirmedEvent->uuid}/checkpoint/done", ['checkpoint_id' => $s->eventCheckpoint->id])
            ->assertOk()
            ->assertJsonPath('data.checkpoint_done', true)
            ->assertJsonPath('data.room_done', true);

        $this->assertTrue((bool) $s->eventCheckpoint->fresh()->done);
        $this->assertTrue((bool) $s->eventRoom->fresh()->done);
    }

    public function test_guest_cannot_toggle_checkpoint_of_other_booking(): void
    {
        $s = BookingScenario::create();
        // Checkpoint einer anderen Buchung
        $otherRoom = EventRoom::factory()->create(['event_id' => $s->event->id, 'room_id' => $s->room->id]);
        $other = EventCheckpoint::factory()->create(['event_room_id' => $otherRoom->id, 'checkpoint_id' => Checkpoint::factory()->create(['room_id' => $s->room->id])->id]);

        $this->postJson("/bookings/{$s->confirmedEvent->uuid}/checkpoint/done", ['checkpoint_id' => $other->id])
            ->assertNotFound();

        $this->assertFalse((bool) $other->fresh()->done);
    }

    public function test_old_route_without_booking_is_gone(): void
    {
        $s = BookingScenario::create();

        $this->postJson('/bookings/checkpoint/done', ['checkpoint_id' => $s->eventCheckpoint->id]);

        $this->assertFalse((bool) $s->eventCheckpoint->fresh()->done);
    }

    public function test_unknown_booking_uuid_is_not_found(): void
    {
        $s = BookingScenario::create();

        $this->postJson('/bookings/00000000-0000-0000-0000-000000000000/checkpoint/done', ['checkpoint_id' => $s->eventCheckpoint->id])
            ->assertNotFound();

        $this->assertFalse((bool) $s->eventCheckpoint->fresh()->done);
    }
}
