<?php

namespace Tests\Feature;

use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Newsletter-Empfänger im Admin-Bereich (Verwalter und Admin).
 */
class NewsletterTest extends TestCase
{
    public function test_edit_page_shows_recipient(): void
    {
        $s = BookingScenario::create();

        // Die Ansicht nutzte noch Form:: (Laravel Collective, nicht mehr installiert) und brach mit 500 ab.
        $this->actingAs($s->user('verwalter'))->get("/admin/newsletter/{$s->newsletter->id}/edit")
            ->assertOk()
            ->assertSee('eva@example.com')
            ->assertSee('action="'.route('newsletter.update', $s->newsletter).'"', false);
    }

    public function test_manager_updates_recipient_and_flags(): void
    {
        $s = BookingScenario::create();

        // Nicht angekreuzte Checkboxen werden nicht gesendet: bookings fällt weg, members kommt dazu.
        $this->actingAs($s->user('verwalter'))->patch("/admin/newsletter/{$s->newsletter->id}", [
            'name' => 'Muster',
            'firstname' => 'Eva Maria',
            'email' => 'eva@example.com',
            'members' => '1',
        ])->assertRedirect('/admin/newsletter');

        $newsletter = $s->newsletter->fresh();
        $this->assertSame('Eva Maria', $newsletter->firstname);
        $this->assertFalse($newsletter->bookings);
        $this->assertTrue($newsletter->members);
    }

    public function test_manager_deletes_recipient(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))->delete("/admin/newsletter/{$s->newsletter->id}")
            ->assertRedirect('/admin/newsletter');

        $this->assertNull($s->newsletter->fresh());
    }
}
