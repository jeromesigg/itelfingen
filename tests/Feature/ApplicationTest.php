<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use App\Notifications\AdminPushNotification;
use App\Notifications\ApplicationCreatedNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Öffentliche Bewerbung für die Genossenschaft (POST applications/store).
 */
class ApplicationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        config(['honeypot.enabled' => false]);
    }

    private function applicationData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Muster',
            'firstname' => 'Erika',
            'organisation' => '',
            'street' => 'Dorfstrasse',
            'house_number' => '2',
            'zipcode' => 8000,
            'city' => 'Zürich',
            'email' => 'erika@example.com',
            'telephone' => '079 765 43 21',
            'why' => 'Ich bin seit Jahren Gast im Haus.',
            'comment' => '',
            'statutes' => '1',
        ], $overrides);
    }

    public function test_application_page_renders(): void
    {
        $this->get('/applications')->assertOk();
    }

    public function test_application_is_stored_and_notified(): void
    {
        $this->from('/applications')->post('/applications/store', $this->applicationData())
            ->assertRedirect('/applications')
            ->assertSessionHas('success');

        $application = Application::sole();
        $this->assertSame('Erika', $application->firstname);
        $this->assertSame(8000, $application->plz);
        Notification::assertSentTo($application, ApplicationCreatedNotification::class);
    }

    public function test_application_sends_push_to_managers(): void
    {
        $verwalter = User::factory()->verwalter()->create();
        $verwalter->updatePushSubscription('https://push.example.com/1', 'key', 'token');

        $this->from('/applications')->post('/applications/store', $this->applicationData());

        Notification::assertSentTo($verwalter, AdminPushNotification::class, fn (AdminPushNotification $notification) => $notification->title === 'Neue Bewerbung'
            && $notification->body === 'Erika Muster'
            && $notification->url === route('applications.show', Application::sole()->id));
    }

    public function test_application_form_cannot_set_internal_fields(): void
    {
        $this->from('/applications')->post('/applications/store', $this->applicationData([
            'refuse' => 1,
            'invoice_send' => 1,
            'bexio_user_id' => 99,
            'bexio_invoice_id' => 99,
        ]));

        $application = Application::sole();
        $this->assertFalse((bool) $application->refuse);
        $this->assertFalse((bool) $application->invoice_send);
        $this->assertNull($application->bexio_user_id);
        $this->assertNull($application->bexio_invoice_id);
    }

    public function test_incomplete_application_is_rejected_without_server_error(): void
    {
        $this->from('/applications')->post('/applications/store', [])
            ->assertRedirect('/applications')
            ->assertSessionHasErrors(['name', 'street', 'zipcode', 'city', 'email', 'why', 'statutes']);

        $this->assertSame(0, Application::count());
        // Die Seite zeigt, was fehlt (bisher zeigte sie keine Fehler an).
        $this->get('/applications')->assertSee('Bitte prüfe deine Angaben');
    }

    public function test_reason_without_whitespace_is_rejected_as_spam(): void
    {
        $this->post('/applications/store', $this->applicationData(['why' => 'spam-link']))->assertStatus(422);

        $this->assertSame(0, Application::count());
    }

    public function test_application_without_accepted_statutes_is_rejected(): void
    {
        $this->from('/applications')->post('/applications/store', $this->applicationData(['statutes' => null]))
            ->assertRedirect('/applications')
            ->assertSessionHasErrors(['statutes' => 'Bitte akzeptiere die Statuten.']);

        $this->assertSame(0, Application::count());
    }

    public function test_form_is_replaced_by_confirmation_after_submit(): void
    {
        $this->from('/applications')->post('/applications/store', $this->applicationData());

        $this->get('/applications')
            ->assertSee('Danke für deine Bewerbung!')
            ->assertDontSee('Bewerbung absenden');
    }
}
