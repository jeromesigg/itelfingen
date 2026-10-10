<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Notifications\ContactCreatedNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Öffentliches Kontaktformular (POST contacts).
 */
class ContactTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        config(['honeypot.enabled' => false]);
    }

    public function test_contact_form_stores_message_and_notifies(): void
    {
        $this->from(route('home'))->post('/contacts', [
            'name' => 'Max Muster',
            'email' => 'max@example.com',
            'subject' => 'Anfrage',
            'content' => 'Ist das Haus im Mai frei?',
        ])->assertRedirect(route('home').'#contact')->assertSessionHas('success_contact');

        $contact = Contact::sole();
        $this->assertSame('Max Muster', $contact->name);
        $this->assertFalse((bool) $contact->done);
        Notification::assertSentTo($contact, ContactCreatedNotification::class);
    }

    public function test_contact_form_cannot_set_internal_fields(): void
    {
        $this->from(route('home'))->post('/contacts', [
            'name' => 'Max Muster',
            'email' => 'max@example.com',
            'subject' => 'Anfrage',
            'content' => 'Ist das Haus im Mai frei?',
            'done' => 1,
            'user_id' => 1,
        ]);

        $contact = Contact::sole();
        $this->assertFalse((bool) $contact->done);
        $this->assertNull($contact->user_id);
    }

    public function test_message_without_whitespace_is_rejected_as_spam(): void
    {
        $this->post('/contacts', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'subject' => 'x',
            'content' => 'klicke-hier-www.spam.example',
        ])->assertStatus(422);

        $this->assertSame(0, Contact::count());
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->from(route('home'))->post('/contacts', [
            'name' => 'Max Muster',
            'email' => 'keine-adresse',
            'subject' => 'Anfrage',
            'content' => 'Ist das Haus frei?',
        ])->assertSessionHasErrors('email', null, 'contact');

        $this->assertSame(0, Contact::count());
    }
}
