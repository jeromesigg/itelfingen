<?php

namespace Tests\Feature;

use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Checkliste Reinigung im Admin-Bereich: Räume und Checkpunkte pflegen (Verwalter und Admin).
 */
class ChecklistAdminTest extends TestCase
{
    public function test_checkpoint_edit_page_shows_checkpoint(): void
    {
        $s = BookingScenario::create();

        // Die Ansicht war eine Kopie der FAQ-Ansicht und brach mit "Undefined variable $faq" ab.
        $this->actingAs($s->user('verwalter'))->get("/admin/checkpoints/{$s->checkpoint->id}/edit")
            ->assertOk()
            ->assertSee('Herd reinigen')
            ->assertSee('action="'.route('checkpoints.update', $s->checkpoint).'"', false);
    }

    public function test_manager_updates_checkpoint(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))->patch("/admin/checkpoints/{$s->checkpoint->id}", [
            'name' => 'Backofen reinigen',
            'description' => 'Auch das Blech',
            'room_id' => $s->room->id,
            'sort-index' => 3,
            'archive_status_id' => config('status.aktiv'),
        ])->assertRedirect('/admin/checkpoints');

        $checkpoint = $s->checkpoint->fresh();
        $this->assertSame('Backofen reinigen', $checkpoint->name);
        $this->assertSame(3, $checkpoint['sort-index']);
    }

    public function test_room_edit_page_shows_room(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))->get("/admin/rooms/{$s->room->id}/edit")
            ->assertOk()
            ->assertSee('Küche');
    }
}
