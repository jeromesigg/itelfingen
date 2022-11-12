<?php

namespace App\Helper;

use App\Models\Event;
use App\Models\Position;
use App\Models\PricelistPosition;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Spatie\GoogleCalendar\Event as Event_API;

class Helper
{
    static function PrintParking(Event $event){
        $outputFile = Storage::disk('local')->path('files/Parkkarten/'.$event['id'].'.pdf');
        // fill data
        Helper::fillPDF(storage_path('app/files/Parkkarte.pdf'), $outputFile, $event);
        //output to browser
        return $outputFile;
    }

    static function fillPDF($file, $outputFile, Event $event)
    {
        define('FPDF_FONTPATH',public_path('fonts'));
        $fpdi = new FPDI;
        $fpdi->AddFont("TitilliumWeb-Light");
        // merger operations
        $count = $fpdi->setSourceFile($file);

        $first_row = 28;
        $row_height = 10;

        $pl_position = PricelistPosition::where('bexio_code','=',210)->first();
        if ($pl_position) {
            $positions = Position::where('pricelist_position_id',$pl_position['id'])->where('event_id',$event['id'])->first();
            if ($positions) {
                $amount = $positions['amount'];
            }
            else{
                $amount = 0;
            }
        }
        else{
            $amount = 0;
        }

        $parking = max($amount, 3);
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

    static function EventToGoogleCalendar(Event $event)
    {
        $event_api = new Event_API;
        $event_api->name = $event['firstname'] . ' ' . $event['name'] . ' - ' . $event['group_name'] . ' - ' . $event['telephone'];
        $event_api->startDate = Carbon::parse($event->start_date);
        $event_api->endDate = Carbon::parse($event->end_date)->addDay();
        $event_api->save();
    }

}
