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
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

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
        if(count($events)>0) {
            $this->info(count($events) . ' Emails versendet.');
        }
    }

    public function SendApplicationInvoices(){
        $date = Carbon::today()->addweeks(-2);
        $applications = Application::where('invoice_send', false)->whereNotNull('bexio_user_id')->where('created_at','<=', $date )->where('refuse', false)->get();

        foreach($applications as $application){
            $this->SendInvoice($application);
        }
        if(count($applications)>0) {
            $this->info(count($applications) . ' Rechnungen versendet.');
        }
    }

    public function GetLastInfoPDF(Event $event)
    {
        $PdfPath = storage_path('app/contracts/Infos_vor_Buchung.pdf');

        $data["email"] = $event['email'];
        $data["title"] = "Die letzten Infos zu ihrem Aufenthalt im Ferienhaus Itelfingen";
        $data["firstname"] = $event['firstname'];
        $data["name"] = $event['name'];
        $data["code"] = $event['code'];
        $outputFile = $this->PrintParking($event);
        Mail::send('emails.last_infos', $data, function($message)use($data, $PdfPath, $outputFile) {
            $message->to($data["email"], $data['firstname'] . ' ' . $data["name"])
                    ->bcc(config('mail.from.address'), config('mail.from.name'))
                    ->subject($data["title"])
                    ->attach($PdfPath, [
                        'as'    => 'Infos_vor_Buchung.pdf',
                        'mime'   => 'application/pdf',
                    ])
                    ->attach($outputFile, [
                        'as'    => 'Parkkarte.pdf',
                        'mime'   => 'application/pdf',
                    ]);
        });

        $event->update(['last_info' => true]);
    }

    public function PrintParking(Event $event){
        $outputFile = Storage::disk('local')->path('files/Parkkarten/'.$event['id'].'.pdf');
        // fill data
        $this->fillPDF(storage_path('app/files/Parkkarte.pdf'), $outputFile, $event);
        //output to browser
        return $outputFile;
    }

    public function fillPDF($file, $outputFile, Event $event)
    {
        define('FPDF_FONTPATH',public_path('fonts'));
        $fpdi = new FPDI;
        $fpdi->AddFont("TitilliumWeb-Light");
        // merger operations
        $count = $fpdi->setSourceFile($file);

        $first_row = 28;
        $row_height = 10;

        $pl_position = PricelistPosition::where('bexio_code','=',210)->first();
        $positions = Position::where('pricelist_position_id',$pl_position['id'])->where('event_id',$event['id'])->first();
        $parking = $positions['amount']<=3 ? 3 : $positions['amount'];
        $parking .=  ' Parkplätze';

        $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');
        $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');

        $write_array = array(
            array(
                'text' =>   $event['firstname'] . ' ' . $event['name']
            ),
            array(
                'text' => $parking
            ),
            array(
                'text' => $start_date  . ' bis ' . $end_date
            ),
        );

        $phone_array = array(
            array(
                'text' => 'Vermieter: verwalter@itelfingen.ch'
            ),
            array(
                'text' => 'Mieter: ' . $event['telephone']
            )
        );


        for ($i=1; $i<=$count; $i++) {
            $template   = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);
            $fpdi->SetFont("TitilliumWeb-Light", "", 16);
            $fpdi->SetTextColor(0, 0, 0);
            $fpdi->Ln($first_row);
            foreach ($write_array as $write) {
                $fpdi->Ln($row_height);

                $write['text'] = iconv('utf-8', 'cp1252', $write['text']);
                $fpdi->Cell(0,10, $write['text'], 0,1,"C");
            }
            $fpdi->Ln(29);
            $fpdi->Cell(20);
            foreach ($phone_array as $phone) {

                $phone['text'] = iconv('utf-8', 'cp1252', $phone['text']);
                $fpdi->Cell(100,0, $phone['text']);
            }
        }
        return $fpdi->Output($outputFile, 'F');
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


            $application->update([
                    'invoice_send' => true,
                    'bexio_invoice_id' => $invoice['id']
                ]
            );

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
