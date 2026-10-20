<?php

namespace Tests\Feature\Security;

use App\Models\Homepage;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Bemerkungen und Zusatztext für Mails auf der Buchungsübersicht (admin.homepages.comment_update/mail_text_update).
 * Verwalter dürfen nur diese beiden Felder ändern, nicht die übrigen Inhalte der Startseite (nur Admin).
 */
class HomepageNotesTest extends TestCase
{
    public function test_manager_updates_booking_comment(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))
            ->from('/admin/events')
            ->patch(route('admin.homepages.comment_update', 1), ['event_comment' => 'Schlüssel bei Nachbarn'])
            ->assertRedirect('/admin/events');

        $this->assertSame('Schlüssel bei Nachbarn', Homepage::find(1)->event_comment);
    }

    public function test_manager_updates_additional_mail_text(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))
            ->patch(route('admin.homepages.mail_text_update', 1), ['additional_mail_text' => 'Bitte Hausschuhe mitnehmen.']);

        $this->assertSame('Bitte Hausschuhe mitnehmen.', Homepage::find(1)->additional_mail_text);
    }

    public function test_notes_cannot_change_other_homepage_content(): void
    {
        $s = BookingScenario::create();
        $before = Homepage::find(1)->only(['title', 'address', 'phone', 'mail', 'additional_mail_text', 'event_comment']);
        $foreign = ['title' => 'Gehackt', 'address' => 'Irgendwo', 'phone' => '000', 'mail' => 'x@example.com'];

        $this->actingAs($s->user('verwalter'))
            ->patch(route('admin.homepages.comment_update', 1), $foreign + ['event_comment' => 'Neu', 'additional_mail_text' => 'Fremd']);
        $this->actingAs($s->user('verwalter'))
            ->patch(route('admin.homepages.mail_text_update', 1), $foreign + ['additional_mail_text' => 'Neu', 'event_comment' => 'Fremd']);

        $after = Homepage::find(1);
        $this->assertSame($before['title'], $after->title);
        $this->assertSame($before['address'], $after->address);
        $this->assertSame($before['phone'], $after->phone);
        $this->assertSame($before['mail'], $after->mail);
        // Jede Aktion ändert nur ihr eigenes Feld
        $this->assertSame('Neu', $after->event_comment);
        $this->assertSame('Neu', $after->additional_mail_text);
    }
}
