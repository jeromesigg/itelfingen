<?php

namespace App\Console\Commands;

use Notification;
use Carbon\Carbon;
use App\Helper\GlutzAPI;
use App\Models\Room;
use App\Models\Event;
use App\Models\Homepage;
use App\Models\Newsletter;
use App\Models\Application;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Console\Command;
use App\Models\PricelistPosition;
use Revolution\Google\Sheets\Facades\Sheets;
use jeremykenedy\Slack\Laravel\Facade as Slack;
use App\Notifications\EventFeedbackNotification;
use App\Notifications\EventLastInfosNotification;
use App\Notifications\ApplicationInvoiceNotification;
use App\Events\ApplicationCreatedEvent;
use App\Services\BexioApiService;

class DailyTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a task daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->SendEventLastInfos();
        $this->SendApplicationInvoices();
        $this->SendNextEventToSlack();
        $this->DeleteGlutzUsers();
    }

    public function SendEventLastInfos()
    {
        $date = Carbon::today()->addWeeks(2);
        $events = Event::where('last_info', false)->whereNotNull('code')->where('start_date', '<=', $date)->where('event_status_id', '=', config('status.event_bestaetigt'))->get();
        $homepage = Homepage::FindOrFail(1);
        $additional_text = $homepage['additional_mail_text'] ? $homepage['additional_mail_text'] : '';
        foreach ($events as $event) {
            if ($event->event_rooms->count() === 0) {
                $rooms = Room::where('archive_status_id', config('status.aktiv'))->orderBy('sort-index')->get();
                foreach ($rooms as $room) {
                    $event_room = $event->event_rooms()->create([
                        'room_id' => $room->id,
                    ]);
                    foreach ($room->checkpoints()->get() as $checkpoint) {
                        $event_room->event_checkpoints()->create([
                            'checkpoint_id' => $checkpoint->id,
                        ]);
                    }
                }
            }
            Notification::send($event, new EventLastInfosNotification($event, $additional_text));
            $event->update(['last_info' => true]);
        }        if (count($events) > 0) {
            $this->info(count($events).' Letzte Infos-Emails versendet.');
        }
    }

    public function SendFeedbackMails()
    {
        $date = Carbon::today();
        $events = Event::where('feedback_mail', false)->where('end_date', '<=', $date)->where('event_status_id', '=', config('status.event_bestaetigt'))->get();

        foreach ($events as $event) {
            Notification::send($event, new EventFeedbackNotification($event));
            $event->update(['feedback_mail' => true]);
        }        if (count($events) > 0) {
            $this->info(count($events).' Feedback-Mails versendet.');
        }
    }

    public function SendApplicationInvoices()
    {
        $date = Carbon::today()->addweeks(-2);
        $applications = Application::where('invoice_send', false)->where('created_at', '<=', $date)->where('refuse', false)->get();

        foreach ($applications as $application) {
            ApplicationCreatedEvent::dispatch($application);
            $this->SendApplicationInvoice($application);
        }
        if (count($applications) > 0) {
            $this->info(count($applications).' Rechnungen versendet.');
        }
    }

    public function SendApplicationInvoice($application)
    {
        $pl_position = PricelistPosition::where('bexio_code', '=', 300)->first();

        if (!isset($application['bexio_invoice_id']) && isset($application['bexio_user_id'])) {
            $data = [
                        'title' => 'Dein Genossenschaftsschein der Genossenschaft Ferienhaus Itelfingen',
                        'contact_id' => $application->bexio_user_id,
                        'user_id' => 1,
                        'is_valid_from' => now(),
                        'is_valid_to' => Carbon::today()->addDays(30),
                        'api_reference' => $application['id'],
                        'positions' => [
                            [
                                'amount' => 1,
                                'type' => 'KbPositionArticle',
                                'tax_id' => 16,
                                'article_id' => $pl_position['bexio_id'],
                                'unit_price' => $pl_position['price'],
                                'discount_in_percent' => 0,
                            ],
                        ],
                    ];
            $invoice = app(BexioApiService::class)->post('kb_invoice', $data);
            $title = 'Deine Rechnung zum Genossenschaftsschein der Genossenschaft Ferienhaus Itelfingen';

            $data_send = [
                        'recipient_email' => config('mail.invoice_mail'),
                        'subject' => $title,
                        'message' => $application['firstname'].' '.$application['name'].': [Network Link]',
                        'mark_as_open' => true,
                    ];
            app(BexioApiService::class)->post('kb_invoice/'.$invoice['id'].'/send', $data_send);
                
            $invoice = app(BexioApiService::class)->get('kb_invoice/'.$invoice['id']);
            $application->update([
                'bexio_invoice_id' => $invoice['id'],
            ]);
        } else {
            $invoice = app(BexioApiService::class)->get('kb_invoice/'.$application['bexio_invoice_id']);
        }
        if (isset($invoice['id'])) {

            Notification::send($application, new ApplicationInvoiceNotification($application, $invoice));

            $application->update([
                'invoice_send' => true,
            ]);

            Newsletter::updateOrCreate (
                ['email' => $application['email']],
                [
                    'firstname' => $application['firstname'],
                    'name' => $application['name'],
                    'members' => true
                ]);

            //            if (config('app.env') == 'production') {
            // Write to Google Sheet
            $array = [[
                'ID' => $application['id'],
                'Datum' => Carbon::parse($application['created_at'])->format('d.m.Y'),
                'Anrede' => '',
                'Vorname' => $application['firstname'],
                'Name' => $application['name'],
                'Organisation' => $application['organisation'],
                'Strasse' => $application['street'] . ' ' . $application['house_number'],
                'PLZ' => $application['plz'],
                'Ort' => $application['city'],
                'E-Mail' => $application['email'],
                'Telefon' => $application['telephone'],
                'Grund' => $application['why'],
                'Bemerkung' => $application['comment'],
                'Bexio User' => $application['bexio_user_id'],
                'Bexio Rechnung' => $application['bexio_invoice_id'],
            ]];
            // Add new sheet to the configured google spreadsheet
            Sheets::spreadsheet(config('google.spreadsheet_id'))->sheet('Bewerbungen')->append($array);
            //            }
        }
    }

    public function SendNextEventToSlack()
    {
        $date = Carbon::today()->addDays(2);
        $events = Event::where('start_date', '=', $date)->where('event_status_id', '=', config('status.event_bestaetigt'))->get();

        foreach ($events as $event) {
            $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');
            $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');

            Slack::to(config('slack.event_channel'))->send('Die nächste Buchung von '.$start_date.' bis '.$end_date.":\n".
                    $event['firstname'].' '.$event['name'].' - '.$event['group_name']."\n".
                    'Telefon Nummer: '.$event['telephone']);
        }
        if (count($events) > 0) {
            $this->info(count($events).' nächste Buchungen gemeldet.');
        }
    }

    public function DeleteGlutzUsers()
    {
        $date = Carbon::today()->addDays(-3);
        $events = Event::where('end_date', '<', $date)->whereNotNull('glutz_user_id')->get();

        foreach ($events as $event) {
            if (GlutzAPI::deleteUser($event['glutz_user_id'])){
                $event->update(['glutz_user_id' => null]);
            }
        }
        if (count($events) > 0) {
            $this->info(count($events).' alte Glutz-Nutzer gelöscht.');
        }
    }
}
