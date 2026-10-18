<?php

namespace Tests\Feature;

use App\Enums\ArchiveState;
use App\Models\Person;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Öffentliche Seite "Über uns" mit dem Vorstand der Genossenschaft.
 */
class AboutUsPageTest extends TestCase
{
    public function test_board_member_with_photo_is_shown_with_name_as_alt_text(): void
    {
        $s = BookingScenario::create();

        $this->get('/about_us')
            ->assertOk()
            ->assertSee('Der Vorstand')
            ->assertSee('alt="'.$s->person->name.'"', false)
            ->assertSee($s->person->function);
    }

    public function test_board_member_without_photo_gets_initials_instead_of_external_image(): void
    {
        $s = BookingScenario::create();
        $s->person->update(['photo_id' => null]);

        $this->get('/about_us')
            ->assertOk()
            ->assertSee('HH')
            ->assertDontSee('loremflickr');
    }

    public function test_archived_board_members_are_hidden(): void
    {
        BookingScenario::create();
        Person::create(['name' => 'Ehemalige Person', 'function' => 'Kasse', 'sort-index' => 2, 'archive_status_id' => ArchiveState::Archiviert]);

        $this->get('/about_us')->assertDontSee('Ehemalige Person');
    }
}
