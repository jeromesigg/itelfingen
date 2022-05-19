<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Event;
use App\Models\Position;
use App\Models\PricelistPosition;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Ixudra\Curl\Facades\Curl;
use Revolution\Google\Sheets\Facades\Sheets;

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
    }

    public function SendEventLastInfos(){
        $date = Carbon::today()->addweeks(2);
        $events = Event::where('last_info', false)->whereNotNull('code')->where('start_date','<=', $date )->where('event_status_id','=', config('status.event_bestaetigt'))->get();

        foreach($events as $event){
            $this->GetLastInfoPDF($event);
        }
        $this->info(count($events) . ' Emails versendet.');
    }

    public function SendApplicationInvoices(){
        $date = Carbon::today()->addweeks(-2);
        $applications = Application::where('invoice_send', false)->whereNotNull('bexio_user_id')->where('created_at','<=', $date )->where('refuse', false)->get();

        foreach($applications as $application){
            $this->SendInvoice($application);
        }
        $this->info(count($applications) . ' Rechnungen versendet.');
    }

    public function GetLastInfoPDF(Event $event)
    {
        $PdfPath = storage_path('app/contracts/Infos_vor_Buchung.pdf');

        $data["email"] = $event['email'];
        $data["title"] = "Die letzten Infos zu ihrem Aufenthalt.";
        $data["firstname"] = $event['firstname'];
        $data["name"] = $event['name'];
        $data["code"] = $event['code'];

        Mail::send('emails.last_infos', $data, function($message)use($data, $PdfPath) {
            $message->to($data["email"], $data['firstname'] . ' ' . $data["name"])
                    ->bcc(config('mail.from.address'), config('mail.from.name'))
                    ->subject($data["title"])
                    ->attach($PdfPath, [
                        'as'    => 'Infos_vor_Buchung.pdf',
                        'mime'   => 'application/pdf',
                    ]);
        });

        $event->update(['last_info' => true]);
    }

    public function SendInvoice( Application $application)
    {

        $pl_position = PricelistPosition::where('bexio_code','=',300)->first();

        $invoice = Curl::to('https://api.bexio.com/2.0/kb_invoice')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->withData(
                array(
                    'title' => 'Dein Genossenschaftsschein der Genossenschaft Ferienhaus Itelfingen',
                    'contact_id' => $application->bexio_user_id,
                    'user_id' => 1,
                    'is_valid_from' => now(),
                    'is_valid_to' => Carbon::today()->addDays(30),
                    'api_reference' => $application['id'],
                    'positions' => array(
                        array(
                            'amount' => 1,
                            'type' => 'KbPositionArticle',
                            'tax_id' => 16,
                            'article_id' => $pl_position['bexio_id'],
                            'unit_price' => $pl_position['price'],
                            'discount_in_percent' =>  0,
                        )
                    )
                )
            )
            ->asJson(true)
            ->post();
        if(isset($invoice['id'])){

            $title = 'Deine Rechnung zum Genossenschaftsschein der Genossenschaft Ferienhaus Itelfingen';

            $message = 'Guten Tag ' . $application['firstname'] . ' ' . $application['name'] .',

            Vielen Dank für dein Interesse an der Genossenschaft Ferienhaus Itelfingen und deiner Bewerbung als Genossenschafter:in.

            Unter folgendem Link kannst Du Deine Rechnung für Deinen Genossenschaftsschein über CHF ' . $invoice['total'] .  ' ansehen:
            [Network Link]

            Wir bitten um Bezahlung über einer der zur Verfügung stehenden Zahlungsmöglichkeiten.
            Für Rückfragen zu dieser Rechnung stehen wir jederzeit gerne zur Verfügung.

            Freundliche Grüsse,
            Das Ferienhaus Itelfingen';

            Curl::to('https://api.bexio.com/2.0/kb_invoice/' . $invoice['id'] . '/send')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->withData(
                    array(
                        'recipient_email' => $application['email'],
                        'subject' => $title,
                        'message' => $message,
                        'mark_as_open' => true
                    )
                )
                ->asJson(true)
                ->post();

//
//            $application->update([
//                    'invoice_send' => true,
//                    'bexio_invoice_id' => $invoice['id']
//                ]
//            );

            // Write to Google Sheet
            $array = [[
                'ID' => $application['id'],
                'Datum' =>  Carbon::parse($application['created_at'])->format('d.m.Y'),
                'Anrede' => $application->salutation['name'],
                'Name' => $application['name'],
                'Vorname' => $application['firstname'],
                'Organisation' => $application['organisation'],
                'E-Mail' => $application['email'],
                'Strasse' => $application['street'],
                'PLZ' => $application['plz'],
                'Ort' => $application['city'],
                'Telefon' => $application['telephone'],
                'Grund' => $application['why'],
                'Bemerkung' => $application['comment'],
                'Bexio User' => $application['bexio_user_id'],
                'Bexio Rechnung' => $application['bexio_invoice_id'],
            ]];
            // Add new sheet to the configured google spreadsheet
            Sheets::spreadsheet(config('google.spreadsheet_id'))->sheet('Bewerbungen')->append($array);

        }
    }
}
