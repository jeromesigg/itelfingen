<?php

namespace Tests\Browser;

use App\Models\Checkpoint;
use App\Models\EventCheckpoint;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\BookingScenario;

/**
 * Checkliste im Kundenbereich (bookings/{uuid}/checklist): Abhaken speichert sofort, Zähler und Raum-Status folgen.
 */
class CustomerChecklistTest extends DuskTestCase
{
    public function test_guest_checks_off_points_and_progress_follows(): void
    {
        $s = BookingScenario::create();
        $second = EventCheckpoint::factory()->create([
            'event_room_id' => $s->eventRoom->id,
            'checkpoint_id' => Checkpoint::factory()->create(['room_id' => $s->room->id, 'name' => 'Boden wischen'])->id,
        ]);
        $first = $s->eventCheckpoint;
        $room = "[data-room=\"{$s->eventRoom->id}\"]";

        $this->browse(function (Browser $browser) use ($s, $first, $second, $room) {
            $browser->visit("/bookings/{$s->confirmedEvent->uuid}/checklist")
                ->assertSeeIn('[data-progress-done]', '0')
                ->click("{$room} summary")
                ->waitFor("label[for=\"checkpoint-{$first->id}\"]")
                ->click("label[for=\"checkpoint-{$first->id}\"]")
                ->waitForTextIn("{$room} [data-room-done]", '1')
                ->assertSeeIn('[data-progress-done]', '1')
                ->assertAttributeContains("{$room} [data-room-icon]", 'class', 'bg-gray-100');

            // Zweiter Punkt: Raum erledigt
            $browser->click("label[for=\"checkpoint-{$second->id}\"]")
                ->waitForTextIn("{$room} [data-room-done]", '2')
                ->assertAttributeContains("{$room} [data-room-icon]", 'class', 'bg-gladegreen');

            // Nach dem Neuladen bleibt alles gespeichert
            $browser->refresh()
                ->assertSeeIn('[data-progress-done]', '2')
                ->click("{$room} summary")
                ->click("label[for=\"checkpoint-{$first->id}\"]")
                ->waitForTextIn("{$room} [data-room-done]", '1')
                ->assertMissing('#checklist-error:not(.hidden)');
        });

        $this->assertFalse((bool) $first->fresh()->done);
        $this->assertTrue((bool) $second->fresh()->done);
        $this->assertFalse((bool) $s->eventRoom->fresh()->done);
    }
}
