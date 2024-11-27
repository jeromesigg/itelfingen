<?php

namespace App\Helper;

use App\Charts\BookingChart;
use App\Models\Event;
use App\Models\Position;
use App\Models\PricelistPosition;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use jeremykenedy\Slack\Laravel\ServiceProvider;
use setasign\Fpdi\Fpdi;
use Spatie\GoogleCalendar\Event as Event_API;

class Helper
{
    public static function PrintParking(Event $event)
    {
        $outputFile = Storage::disk('local')->path('files/Parkkarten/'.$event['id'].'.pdf');
        // fill data
        Helper::fillPDF(storage_path('app/files/Parkkarte.pdf'), $outputFile, $event);
        //output to browser
        return $outputFile;
    }

    public static function fillPDF($file, $outputFile, Event $event)
    {
        if(!defined('FPDF_FONTPATH')){
            define('FPDF_FONTPATH', public_path('fonts'));
        }
        $fpdi = new FPDI;
        $fpdi->AddFont('TitilliumWeb-Light');
        // merger operations
        $count = $fpdi->setSourceFile($file);

        $first_row = 28;
        $row_height = 10;

        $pl_position = PricelistPosition::where('bexio_code', '=', 210)->first();
        if ($pl_position) {
            $positions = Position::where('pricelist_position_id', $pl_position['id'])->where('event_id', $event['id'])->first();
            if ($positions) {
                $amount = $positions['amount'];
            } else {
                $amount = 0;
            }
        } else {
            $amount = 0;
        }

        $parking = max($amount, 3);
        $parking .= ' Parkplätze';

        $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');
        $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');

        $write_array = [
            [
                'text' => $event['firstname'].' '.$event['name'],
            ],
            [
                'text' => $parking,
            ],
            [
                'text' => $start_date.' bis '.$end_date,
            ],
        ];

        $phone_array = [
            [
                'text' => 'Vermieter: verwalter@itelfingen.ch',
            ],
            [
                'text' => 'Mieter: '.$event['telephone'],
            ],
        ];

        for ($i = 1; $i <= $count; $i++) {
            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $fpdi->useTemplate($template);
            $fpdi->SetFont('TitilliumWeb-Light', '', 16);
            $fpdi->SetTextColor(0, 0, 0);
            $fpdi->Ln($first_row);
            foreach ($write_array as $write) {
                $fpdi->Ln($row_height);

                $write['text'] = iconv('utf-8', 'cp1252', $write['text']);
                $fpdi->Cell(0, 10, $write['text'], 0, 1, 'C');
            }
            $fpdi->Ln(29);
            $fpdi->Cell(20);
            foreach ($phone_array as $phone) {
                $phone['text'] = iconv('utf-8', 'cp1252', $phone['text']);
                $fpdi->Cell(100, 0, $phone['text']);
            }
        }

        return $fpdi->Output($outputFile, 'F');
    }

    public static function EventToGoogleCalendar(Event $event)
    {
        $event_api = new Event_API;
        $event_api->name = $event['firstname'].' '.$event['name'].' - '.$event['group_name'].' - '.$event['telephone'];
        $event_api->startDate = Carbon::parse($event->start_date);
        $event_api->endDate = Carbon::parse($event->end_date)->addDay();
        $event_api->save();
    }

    public static function GetEventUserCheck(Event $event)
    {
        switch ($event->event_status['id']){
            case config('status.event_eigene'):
                $user = '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> Interne Buchung';
                break;
            case config('status.event_storniert'):
                $user = '<i class="fa-solid fa-xmark" style="color: indianred"></i> Storniert';
                break;
            default:
                $user = $event->user ? '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> Verantwortlich: ' . $event->user->username :
                    '<i class="fa-solid fa-xmark" style="color: indianred"></i> ' . 'Kein Benutzer zugewiesen';
                break;
        }
        return $user . '<br>';
    }

    public static function GetEventCleaningMailCheck(Event $event)
    {
        $cleaning_mail = '';
        if($event->event_status['id'] <> config('status.event_storniert')){
            $cleaning_mail = $event->cleaning_mail ? '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> Putzmail versendet' :
                '<i class="fa-solid fa-xmark" style="color: indianred"></i> Kein Putzmail versendet';
            $cleaning_mail .= '<br>';
        }
        return $cleaning_mail;
    }

    public static function GetEventCodeCheck(Event $event)
    {
        $code = '';
        if($event->event_status['id'] <> config('status.event_eigene') && $event->event_status['id'] <> config('status.event_storniert')){
            $code = $event->code ? '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> Turcode: ' . $event->code :
            '<i class="fa-solid fa-xmark" style="color: indianred"></i> ' . 'Kein Code zugewiesen';
            $code .= '<br>';
        }
        return $code;
    }

    public static function GetEventOfferStatus(Event $event)
    {
        $offer = '';
        if($event->event_status['id'] <> config('status.event_eigene') && $event->event_status['id'] <> config('status.event_storniert')) {
            if ($event->bexio_offer_id) {
                $offer = '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> ' . '<a target="_blank" href="https://office.bexio.com/index.php/kb_offer/show/id/' . $event['bexio_offer_id'] . '">Angebot</a> ';
                switch ($event->contract_status['id']) {
                    case config('status.contract_angebot_erstellt') :
                        $offer .= 'erstellt';
                        break;
                    case config('status.contract_angebot_versendet') :
                        $offer .= 'erstellt & versendet';
                        break;
                    default:
                        $offer .= 'erstellt, versendet & akzeptiert';
                        break;
                };
            } else {
                $offer = '<i class="fa-solid fa-xmark" style="color: indianred"></i> ' . 'Kein Angebot erstellt';
            }
            $offer .= '<br>';
        }
        return $offer;
    }

    public static function GetEventInvoiceStatus(Event $event)
    {
        $invoice = '';
        if($event->event_status['id'] <> config('status.event_eigene') && $event->event_status['id'] <> config('status.event_storniert')) {

            if($event->bexio_invoice_id){
                $invoice = '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> ' . '<a target="_blank" href="https://office.bexio.com/index.php/kb_invoice/show/id/'.$event['bexio_invoice_id'].'">Rechnung</a> ';
                switch ($event->contract_status['id']){
                    case config('status.contract_rechnung_erstellt') :
                        $invoice .= 'erstellt';
                        break;
                    case config('status.contract_rechnung_versendet') :
                        $invoice .= 'erstellt & versendet';
                        break;
                };
            }else {
                $invoice = '<i class="fa-solid fa-xmark" style="color: indianred"></i> ' . 'Keine Rechnung erstellt';
            }
        $invoice .= '<br>';
        }
        return $invoice;
    }

    public static function GetChart($time_frame){
        switch ($time_frame){
            case 'quarter':
                $time_frame_SQL = "concat(DATE_FORMAT(start_date, '%Y'),'-Q', QUARTER(start_date)) as timeframe";
                break;
            case 'monthly':
                $time_frame_SQL = "concat(LEFT(DATE_FORMAT(start_date, '%M'),3), ' ', DATE_FORMAT(start_date, '%Y')) as timeframe";
                break;
            case 'daily':
                $time_frame_SQL = "start_date as timeframe";
                break;
            default:
                $time_frame_SQL = "DATE_FORMAT(start_date, '%Y') as timeframe";
                break;
        }

        $events_nights = Event::select(
            DB::raw('sum(total_days) as days'),
            DB::raw('sum(total_people*total_days) as stays'),
            DB::raw($time_frame_SQL),
        )
            ->where('event_status_id','<=',config('status.event_bestaetigt'))
            ->groupBy('timeframe')
            ->orderBy('start_date', 'ASC');

        $timeframe = $events_nights->pluck('timeframe');
        $days = $events_nights->pluck('days');
        $stays = $events_nights->pluck('stays');

        $bookingChart = new BookingChart;
        $bookingChart->labels($timeframe);
        $bookingChart->height(900);
        $bookingChart->dataset('Anzahl Tage', 'line', $days)
            ->color('#92D1C3')
            ->backgroundColor('#92D1C3');
        $bookingChart->dataset('Anzahl Übernachtungen', 'line', $stays)
            ->color('#B47EB3')
            ->backgroundColor('#B47EB3');

        return $bookingChart;
    }

}
