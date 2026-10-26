<?php

namespace Tests\Feature;

use App\Enums\ContractState;
use App\Models\Contact;
use App\Models\Event;
use App\Models\User;
use Tests\TestCase;

/**
 * Admin-Dashboard (/admin): Aufgabenliste und Menü unten für das Handy.
 */
class AdminDashboardTest extends TestCase
{
    public function test_manager_sees_open_tasks(): void
    {
        $this->travelTo('2026-03-02 10:00:00');
        Event::factory()->confirmed()->create(['firstname' => 'Hans', 'name' => 'Muster', 'group_name' => 'Pfadi Uster', 'start_date' => '2026-03-07', 'end_date' => '2026-03-08', 'cleaning_mail' => true]);
        Contact::factory()->create(['name' => 'Anna Meier']);

        $response = $this->actingAs(User::factory()->verwalter()->create())->get('/admin');

        $response->assertOk()
            ->assertSee('Aufgaben')
            ->assertSeeInOrder(['Türcode fehlt', 'Hans Muster - Pfadi Uster'])
            ->assertSeeInOrder(['Anfrage beantworten', 'Anna Meier']);
    }

    public function test_booking_without_group_shows_only_first_and_last_name(): void
    {
        Event::factory()->create(['firstname' => 'Eva', 'name' => 'Graf', 'group_name' => null]);

        $response = $this->actingAs(User::factory()->admin()->create())->get('/admin');

        $response->assertOk()
            ->assertSee('Eva Graf')
            ->assertDontSee('Eva Graf -');
    }

    public function test_next_stays_show_whether_invoice_was_sent(): void
    {
        $this->travelTo('2026-03-02 10:00:00');
        Event::factory()->confirmed()->create(['firstname' => 'Offen', 'start_date' => '2026-02-20', 'end_date' => '2026-02-22']);
        Event::factory()->confirmed()->create(['firstname' => 'Gestellt', 'start_date' => '2026-02-25', 'end_date' => '2026-02-27', 'contract_status_id' => ContractState::RechnungVersendet]);

        $response = $this->actingAs(User::factory()->admin()->create())->get('/admin');

        $response->assertOk()
            ->assertSeeInOrder(['Offen', '✗ Rechnung offen', 'Gestellt', '✓ Rechnung gestellt']);
    }

    public function test_tasks_beyond_five_are_collapsed(): void
    {
        Contact::factory()->count(7)->create();

        $response = $this->actingAs(User::factory()->admin()->create())->get('/admin');

        $response->assertOk()->assertSee('+ 2 weitere anzeigen');
    }

    public function test_bottom_navigation_links_to_main_pages_with_open_contacts_badge(): void
    {
        Contact::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->admin()->create())->get('/admin');

        $response->assertOk();
        $nav = $this->bottomNavigation($response->getContent());
        $this->assertMatchesRegularExpression('/href="\/admin"\s+aria-current="page"/', $nav);
        $this->assertStringContainsString('href="/"', $nav);
        $this->assertStringContainsString('href="'.route('admin.contacts.index').'"', $nav);
        $this->assertStringContainsString('href="'.route('admin.events.index').'"', $nav);
        $this->assertMatchesRegularExpression('/>\s*3\s*<\/span>/', $nav);
    }

    public function test_team_member_sees_neither_tasks_nor_contacts(): void
    {
        Contact::factory()->create();

        $response = $this->actingAs(User::factory()->team()->create())->get('/admin');

        $response->assertOk()
            ->assertDontSee('Aufgaben')
            ->assertDontSee('Anfrage beantworten');
        $this->assertStringNotContainsString(route('admin.contacts.index'), $this->bottomNavigation($response->getContent()));
    }

    private function bottomNavigation(string $html): string
    {
        preg_match('/<nav id="bottom-navigation".*?<\/nav>/s', $html, $nav);

        return $nav[0] ?? '';
    }
}
