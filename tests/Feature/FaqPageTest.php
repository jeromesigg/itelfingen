<?php

namespace Tests\Feature;

use App\Enums\ArchiveState;
use App\Models\Faq;
use App\Models\FaqChapter;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Öffentliche FAQ-Seite: aktive Kapitel mit ihren Fragen, sortiert nach sort-index.
 */
class FaqPageTest extends TestCase
{
    public function test_faq_page_shows_questions_sorted_with_valid_anchors(): void
    {
        $s = BookingScenario::create();

        $this->get('/faq')
            ->assertOk()
            ->assertSee('Häufige Fragen')
            ->assertSee('href="#faq-chapter-'.$s->faqChapter->id.'"', false)
            ->assertSee('id="faq-chapter-'.$s->faqChapter->id.'"', false)
            ->assertSeeInOrder([$s->faq->name, $s->faqWithoutPhoto->name]);
    }

    public function test_questions_follow_sort_index_not_creation_order(): void
    {
        $s = BookingScenario::create();
        $s->faq->update(['sort-index' => 3]);

        $this->get('/faq')->assertSeeInOrder([$s->faqWithoutPhoto->name, $s->faq->name]);
    }

    public function test_archived_questions_and_empty_chapters_are_hidden(): void
    {
        $s = BookingScenario::create();
        $s->faqWithoutPhoto->update(['archive_status_id' => ArchiveState::Archiviert]);
        $empty = FaqChapter::create(['name' => 'Leeres Kapitel', 'sort-index' => 2, 'archive_status_id' => ArchiveState::Aktiv]);
        $archivedChapter = FaqChapter::create(['name' => 'Altes Kapitel', 'sort-index' => 3, 'archive_status_id' => ArchiveState::Archiviert]);
        Faq::create(['name' => 'Alte Frage', 'description' => 'Alt', 'faq_chapter_id' => $archivedChapter->id, 'sort-index' => 1, 'archive_status_id' => ArchiveState::Aktiv]);

        $this->get('/faq')
            ->assertOk()
            ->assertSee($s->faq->name)
            ->assertDontSee($s->faqWithoutPhoto->name)
            ->assertDontSee($empty->name)
            ->assertDontSee('Alte Frage');
    }

    public function test_chapter_without_photo_renders_no_empty_image(): void
    {
        $s = BookingScenario::create();
        $s->faqChapter->update(['photo_id' => null]);

        $this->get('/faq')
            ->assertOk()
            ->assertDontSee('src=""', false);
    }
}
