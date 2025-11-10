<?php

namespace App\Console\Commands;

use Notification;
use Carbon\Carbon;
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
        // $this->SendFeedbackMails();
        $this->SendApplicationInvoices();
        $this->SendNextEventToSlack();
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
        $applications = Application::where('invoice_send', false)->whereNotNull('bexio_user_id')->where('created_at', '<=', $date)->where('refuse', false)->get();

        foreach ($applications as $application) {
            $this->SendApplicationInvoice($application);
        }
        if (count($applications) > 0) {
            $this->info(count($applications).' Rechnungen versendet.');
        }
    }

    public function SendApplicationInvoice($application)
    {
        $pl_position = PricelistPosition::where('bexio_code', '=', 300)->first();

        if (! isset($application['bexio_invoice_id'])) {
            $invoice = Curl::to('https://api.bexio.com/2.0/kb_invoice')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->withData(
                    [
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
                    ]
                )
                ->asJson(true)
                ->post();
            $title = 'Deine Rechnung zum Genossenschaftsschein der Genossenschaft Ferienhaus Itelfingen';

            Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$invoice['id'].'/send')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->withData(
                    [
                        'recipient_email' => config('mail.invoice_mail'),
                        'subject' => $title,
                        'message' => $application['firstname'].' '.$application['name'].': [Network Link]',
                        'mark_as_open' => true,
                    ]
                )
                ->asJson(true)
                ->post();
                
            $invoice = Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$invoice['id'])
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->get();
            $invoice = json_decode($invoice, true);
            $application->update([
                'bexio_invoice_id' => $invoice['id'],
            ]);
        } else {
            $invoice = Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$application['bexio_invoice_id'])
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->get();
            $invoice = json_decode($invoice, true);
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
                'Strasse' => $application['street'],
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
}
