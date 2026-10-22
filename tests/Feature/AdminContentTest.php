<?php

namespace Tests\Feature;

use App\Models\Checkpoint;
use App\Models\Faq;
use App\Models\FaqChapter;
use App\Models\History;
use App\Models\Homepage;
use App\Models\Person;
use App\Models\Picture;
use App\Models\PricelistPosition;
use App\Models\Room;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Inhaltspflege im Admin (nur Admin): anlegen, ändern und löschen über die Formulare,
 * je Bereich einmal. Bild-Uploads prüft Security/ImageUploadTest.
 */
class AdminContentTest extends TestCase
{
    private BookingScenario $s;

    private string $publicDir;

    private string $originalCwd;

    protected function setUp(): void
    {
        parent::setUp();

        $this->s = BookingScenario::create();
        // Die Controller schreiben und löschen Bilder in public/images (relativ bzw. über public_path): nie im echten Ordner
        $this->publicDir = sys_get_temp_dir().'/itelfingen-public-'.uniqid();
        mkdir($this->publicDir.'/images', 0777, true);
        $this->app->usePublicPath($this->publicDir);
        $this->originalCwd = getcwd();
        chdir($this->publicDir);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        array_map('unlink', glob($this->publicDir.'/images/*'));
        rmdir($this->publicDir.'/images');
        rmdir($this->publicDir);

        parent::tearDown();
    }

    /**
     * @return array<string, array{0: string, 1: class-string<Model>, 2: array<string, mixed>, 3: string}>
     *                                                                                                     Bereich => [URI, Modell, Felder zum Anlegen, Feld, das beim Ändern wechselt]
     */
    public static function resources(): array
    {
        return [
            'Räume' => ['admin/rooms', Room::class, ['name' => 'Sauna'], 'name'],
            'Bewertungen' => ['admin/testimonials', Testimonial::class, ['name' => 'Familie Test', 'function' => 'Gäste', 'comment' => 'Schön'], 'comment'],
            'FAQ-Kapitel' => ['admin/faq_chapters', FaqChapter::class, ['name' => 'Abreise'], 'name'],
            'FAQ' => ['admin/faqs', Faq::class, ['name' => 'Gibt es Bettwäsche?', 'description' => 'Ja'], 'description'],
            'Geschichte' => ['admin/histories', History::class, ['title' => 'Umbau', 'subtitle' => '2020', 'description' => 'Neues Dach'], 'description'],
            'Personen' => ['admin/people', Person::class, ['name' => 'Erika Haus', 'function' => 'Kasse'], 'function'],
            'Checkpoints' => ['admin/checkpoints', Checkpoint::class, ['name' => 'Fenster schliessen', 'description' => 'Alle'], 'description'],
        ];
    }

    #[DataProvider('resources')]
    public function test_admin_creates_updates_and_deletes(string $uri, string $model, array $fields, string $changed): void
    {
        $admin = $this->s->user('admin');
        $fields = $this->withRelations($model, $fields);

        $this->actingAs($admin)->post("/{$uri}", $fields)->assertRedirect()->assertSessionHasNoErrors();
        $record = $model::where($fields)->sole();
        $this->assertSame(1, (int) $record->archive_status_id->value);

        $this->actingAs($admin)->patch("/{$uri}/{$record->id}", [$changed => 'Geändert'] + $fields)->assertRedirect();
        $this->assertSame('Geändert', $record->fresh()->{$changed});

        $this->actingAs($admin)->delete("/{$uri}/{$record->id}")->assertRedirect();
        $this->assertNull($record->fresh());
    }

    public function test_history_short_title_follows_title(): void
    {
        $this->actingAs($this->s->user('admin'))->post('/admin/histories', ['title' => 'Neues Dach 2020', 'description' => 'Text']);

        $this->assertSame('neues_dach_2020', History::where('title', 'Neues Dach 2020')->sole()->shorttitle);
    }

    public function test_new_entries_are_appended_at_the_end(): void
    {
        $last = Room::max('sort-index');

        $this->actingAs($this->s->user('admin'))->post('/admin/rooms', ['name' => 'Estrich']);

        $this->assertSame((int) Room::count(), (int) Room::where('name', 'Estrich')->sole()->{'sort-index'});
        $this->assertGreaterThan($last, Room::where('name', 'Estrich')->sole()->{'sort-index'});
    }

    public function test_picture_without_cropped_image_can_be_deleted(): void
    {
        $this->s->picture->update(['cropped_photo_id' => null]);

        $this->actingAs($this->s->user('admin'))->delete("/admin/pictures/{$this->s->picture->id}")->assertRedirect('/admin/pictures');

        $this->assertNull(Picture::find($this->s->picture->id));
    }

    public function test_person_and_picture_with_missing_image_file_can_be_deleted(): void
    {
        // Die Bilddateien der Testdaten existieren nicht: Löschen darf trotzdem nicht scheitern
        $admin = $this->s->user('admin');

        $this->actingAs($admin)->delete("/admin/people/{$this->s->person->id}")->assertRedirect('/admin/people');
        $this->actingAs($admin)->delete("/admin/pictures/{$this->s->picture->id}")->assertRedirect('/admin/pictures');

        $this->assertNull(Person::find($this->s->person->id));
        $this->assertNull(Picture::find($this->s->picture->id));
    }

    public function test_deleting_person_removes_image_file(): void
    {
        $file = $this->publicDir.$this->s->person->photo->file;
        file_put_contents($file, 'bild');

        $this->actingAs($this->s->user('admin'))->delete("/admin/people/{$this->s->person->id}");

        $this->assertFileDoesNotExist($file);
    }

    public function test_price_list_position_update_and_delete(): void
    {
        $admin = $this->s->user('admin');
        $position = PricelistPosition::where('bexio_code', 210)->sole();

        $this->actingAs($admin)->patch("/admin/positions/{$position->id}", ['name' => 'Parkplatz', 'price' => 7, 'show' => '1'])->assertRedirect('/admin/positions');
        $this->assertSame(7.0, (float) $position->fresh()->price);
        $this->assertTrue($position->fresh()->show);

        // Ohne Häkchen schickt der Browser "show" gar nicht
        $this->actingAs($admin)->patch("/admin/positions/{$position->id}", ['name' => 'Parkplatz', 'price' => 7]);
        $this->assertFalse($position->fresh()->show);

        // Kommt in Buchungen vor: nicht löschen, sondern Hinweis aufs Archivieren
        $this->actingAs($admin)->delete("/admin/positions/{$position->id}")
            ->assertRedirect(route('positions.edit', $position))
            ->assertSessionHasErrors('position');
        $this->assertNotNull($position->fresh());

        $unused = PricelistPosition::create(['bexio_code' => 999, 'bexio_id' => 1999, 'name' => 'Alt', 'price' => 1, 'show' => false, 'archive_status_id' => 1]);
        $this->actingAs($admin)->delete("/admin/positions/{$unused->id}")->assertRedirect('/admin/positions');
        $this->assertNull($unused->fresh());
    }

    public function test_picture_name_update_without_new_image(): void
    {
        // Wie das Formular: das versteckte Feld trägt die bestehende ID des Vorschaubilds
        $this->actingAs($this->s->user('admin'))
            ->patch("/admin/pictures/{$this->s->picture->id}", ['name' => 'Seeblick', 'cropped_photo_id' => $this->s->picture->cropped_photo_id])
            ->assertRedirect('/admin/pictures');

        $picture = $this->s->picture->fresh();
        $this->assertSame('Seeblick', $picture->name);
        // Ohne neues Bild bleiben beide Bilder
        $this->assertNotNull($picture->photo_id);
        $this->assertNotNull($picture->cropped_photo_id);
    }

    public function test_homepage_texts_update(): void
    {
        $this->actingAs($this->s->user('admin'))
            ->patch('/admin/homepages/1', ['title' => 'Ferienhaus am See', 'phone' => '041 000 00 00'])
            ->assertRedirect('/admin/homepages');

        $this->assertSame('Ferienhaus am See', Homepage::find(1)->title);
        $this->assertSame('041 000 00 00', Homepage::find(1)->phone);
    }

    /**
     * @param  class-string<Model>  $model
     * @param  array<string, mixed>  $fields
     * @return array<string, mixed>
     */
    private function withRelations(string $model, array $fields): array
    {
        return match ($model) {
            Checkpoint::class => $fields + ['room_id' => $this->s->room->id],
            Faq::class => $fields + ['faq_chapter_id' => $this->s->faqChapter->id],
            default => $fields,
        };
    }
}
