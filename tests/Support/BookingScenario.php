<?php

namespace Tests\Support;

use App\Enums\ArchiveState;
use App\Models\Application;
use App\Models\Checkpoint;
use App\Models\Contact;
use App\Models\Event;
use App\Models\EventCheckpoint;
use App\Models\EventRoom;
use App\Models\Faq;
use App\Models\FaqChapter;
use App\Models\History;
use App\Models\Newsletter;
use App\Models\Person;
use App\Models\Photo;
use App\Models\Picture;
use App\Models\Position;
use App\Models\PricelistPosition;
use App\Models\Room;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Typischer Betrieb des Buchungstools: alle Rollen, Buchungen in verschiedenen Zuständen,
 * Checkliste, offene Kontaktanfrage und Bewerbung.
 *
 * $s = BookingScenario::create();
 */
class BookingScenario
{
    public User $admin;

    public User $verwalter;

    public User $team;

    /** Rolle "Gast": eingeloggt, aber ohne Zugriff auf den Admin-Bereich. */
    public User $gast;

    /** Selbst registriert und noch nicht freigeschaltet (ohne Rolle, inaktiv). */
    public User $registered;

    /** Neue Buchung in der Zukunft mit Positionen und Checkliste. */
    public Event $event;

    /** Bestätigte Buchung mit Rechnung, der Verwalter ist zugeteilt. */
    public Event $confirmedEvent;

    public Event $cancelledEvent;

    public Event $pastEvent;

    public Room $room;

    public Checkpoint $checkpoint;

    public EventRoom $eventRoom;

    public EventCheckpoint $eventCheckpoint;

    public Contact $contact;

    public Application $application;

    // Inhalte der Webseite (Admin)
    public FaqChapter $faqChapter;

    public Faq $faq;

    /** Randfall: FAQ ohne Foto */
    public Faq $faqWithoutPhoto;

    public History $history;

    public Testimonial $testimonial;

    public Person $person;

    /** Randfall: Person ohne Foto */
    public Person $personWithoutPhoto;

    public Picture $picture;

    public PricelistPosition $pricelistPosition;

    public Newsletter $newsletter;

    public static function create(): self
    {
        $s = new self;

        $s->admin = User::factory()->admin()->create(['username' => 'admin_test']);
        $s->verwalter = User::factory()->verwalter()->create(['username' => 'verwalter']);
        $s->team = User::factory()->team()->create(['username' => 'team']);
        $s->gast = User::factory()->gast()->create(['username' => 'gast']);
        $s->registered = User::factory()->inactive()->create(['username' => 'neu']);

        $s->event = Event::factory()->create();
        $s->addPositions($s->event);

        $s->confirmedEvent = Event::factory()->confirmed()->create(['user_id' => $s->verwalter->id]);
        $s->addPositions($s->confirmedEvent);

        $s->cancelledEvent = Event::factory()->cancelled()->create();
        $s->pastEvent = Event::factory()->confirmed()->past()->create();

        $s->room = Room::factory()->create(['name' => 'Küche', 'sort-index' => 1]);
        $s->checkpoint = Checkpoint::factory()->create(['room_id' => $s->room->id, 'name' => 'Herd reinigen']);
        $s->eventRoom = EventRoom::factory()->create(['event_id' => $s->confirmedEvent->id, 'room_id' => $s->room->id]);
        $s->eventCheckpoint = EventCheckpoint::factory()->create([
            'event_room_id' => $s->eventRoom->id,
            'checkpoint_id' => $s->checkpoint->id,
        ]);

        $s->contact = Contact::factory()->create();
        $s->application = Application::factory()->create();

        $s->createContent();

        return $s;
    }

    /**
     * Rollen für Data Provider: Name => Eigenschaft.
     *
     * @return array<string, string>
     */
    public static function roles(): array
    {
        return [
            'admin' => 'admin',
            'verwalter' => 'verwalter',
            'team' => 'team',
            'gast' => 'gast',
        ];
    }

    public function user(string $role): User
    {
        return $this->{$role}->fresh();
    }

    /**
     * Inhalte der Webseite, je ein typisches Objekt und Randfälle ohne Foto.
     */
    private function createContent(): void
    {
        Model::unguarded(function () {
            $aktiv = ArchiveState::Aktiv;
            $photo = fn (string $file) => Photo::create(['file' => $file])->id;

            $this->faqChapter = FaqChapter::create(['name' => 'Anreise', 'sort-index' => 1, 'archive_status_id' => $aktiv, 'photo_id' => $photo('kapitel.png')]);
            $this->faq = Faq::create(['name' => 'Wo parkieren?', 'description' => 'Beim Haus', 'faq_chapter_id' => $this->faqChapter->id, 'photo_id' => $photo('faq.jpg'), 'sort-index' => 1, 'archive_status_id' => $aktiv]);
            $this->faqWithoutPhoto = Faq::create(['name' => 'Gibt es WLAN?', 'description' => 'Ja', 'faq_chapter_id' => $this->faqChapter->id, 'sort-index' => 2, 'archive_status_id' => $aktiv]);
            $this->history = History::create(['title' => 'Gründung', 'shorttitle' => '1950', 'subtitle' => 'Die Anfänge', 'description' => 'Text', 'sort-index' => 1, 'archive_status_id' => $aktiv, 'photo_id' => $photo('geschichte.jpg')]);
            $this->testimonial = Testimonial::create(['name' => 'Familie Muster', 'function' => 'Gäste', 'comment' => 'Wunderbar', 'sort-index' => 1, 'archive_status_id' => $aktiv]);
            $this->person = Person::create(['name' => 'Hans Haus', 'function' => 'Hauswart', 'sort-index' => 1, 'archive_status_id' => $aktiv, 'photo_id' => $photo('person.jpg')]);
            $this->personWithoutPhoto = Person::create(['name' => 'Ohne Foto', 'function' => 'Kasse', 'sort-index' => 2, 'archive_status_id' => $aktiv]);
            $this->picture = Picture::create(['name' => 'Aussicht', 'photo_id' => $photo('bild.jpg'), 'cropped_photo_id' => $photo('bild_cropped.jpg')]);
            $this->pricelistPosition = PricelistPosition::where('bexio_code', 30)->sole();
            $this->newsletter = Newsletter::create(['name' => 'Muster', 'firstname' => 'Eva', 'email' => 'eva@example.com', 'bookings' => true, 'members' => false]);
        });
    }

    /**
     * Positionen wie nach dem Buchungsformular: Hausmiete, Personen, Reinigung.
     */
    private function addPositions(Event $event): void
    {
        $amounts = [20 => 1, 30 => 6, 40 => 4, 210 => 0];
        foreach ($amounts as $code => $amount) {
            Position::create([
                'event_id' => $event->id,
                'pricelist_position_id' => PricelistPosition::where('bexio_code', $code)->value('id'),
                'amount' => $amount,
            ]);
        }
    }
}
