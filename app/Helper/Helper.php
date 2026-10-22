<?php

namespace App\Helper;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Models\Event;
use App\Models\Position;
use App\Models\PricelistPosition;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class Helper
{
    /** Anreden im Buchungs-PDF */
    private const SALUTATIONS = ['Frau', 'Herr'];

    public static function PrintParking(Event $event)
    {
        $outputFile = Storage::disk('local')->path('files/Parkkarten/Parkkarte_'.str_pad($event['id'], 5, '0', STR_PAD_LEFT).'.pdf');
        // fill data
        Helper::fillPDF(storage_path('app/files/Parkkarte.pdf'), $outputFile, $event);

        // output to browser
        return $outputFile;
    }

    public static function fillPDF($file, $outputFile, Event $event)
    {
        if (! defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', public_path('fonts/'));
        }
        $fpdi = new Fpdi;
        $fpdi->AddFont('titilliumweb-light', '', 'titilliumweb-light.php');
        $fpdi->SetFont('titilliumweb-light', '', 12);
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
        app(GoogleCalendarService::class)->addBooking($event);
    }

    public static function GetEventUserCheck(Event $event)
    {
        switch ($event->event_status_id) {
            case BookingState::Eigene:
                $user = '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> Interne Buchung';
                break;
            case BookingState::Storniert:
                $user = '<i class="fa-solid fa-xmark" style="color: indianred"></i> Storniert';
                break;
            default:
                $user = $event->user ? '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> '.$event->user->username :
                    '<i class="fa-solid fa-xmark" style="color: indianred"></i> '.'Kein Benutzer zugewiesen';
                break;
        }

        return $user.'<br>';
    }

    public static function GetEventCleaningMailCheck(Event $event)
    {
        $cleaning_mail = '';
        if ($event->event_status_id !== BookingState::Storniert) {
            $cleaning_mail = $event->cleaning_mail ? '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> Putzmail versendet' :
                '<i class="fa-solid fa-xmark" style="color: indianred"></i> Kein Putzmail versendet';
            $cleaning_mail .= '<br>';
        }

        return $cleaning_mail;
    }

    public static function GetEventCodeCheck(Event $event)
    {
        $code = '';
        if ($event->event_status_id !== BookingState::Eigene && $event->event_status_id !== BookingState::Storniert) {
            $code = $event->code ? '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> Turcode: '.$event->code :
            '<i class="fa-solid fa-xmark" style="color: indianred"></i> '.'Kein Code zugewiesen';
            $code .= '<br>';
        }

        return $code;
    }

    public static function GetEventOfferStatus(Event $event)
    {
        $offer = '';
        if ($event->event_status_id !== BookingState::Eigene && $event->event_status_id !== BookingState::Storniert) {
            if ($event->bexio_offer_id) {
                $offer = '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> '.'<a target="_blank" href="https://office.bexio.com/index.php/kb_offer/show/id/'.$event['bexio_offer_id'].'">Angebot</a> ';
                switch ($event->contract_status_id) {
                    case ContractState::AngebotErstellt :
                        $offer .= 'erstellt';
                        break;
                    case ContractState::AngebotVersendet :
                        $offer .= 'erstellt & versendet';
                        break;
                    default:
                        $offer .= 'erstellt, versendet & akzeptiert';
                        break;
                }
            } else {
                $offer = '<i class="fa-solid fa-xmark" style="color: indianred"></i> '.'Kein Angebot erstellt';
            }
            $offer .= '<br>';
        }

        return $offer;
    }

    public static function GetEventInvoiceStatus(Event $event)
    {
        $invoice = '';
        if ($event->event_status_id !== BookingState::Eigene && $event->event_status_id !== BookingState::Storniert) {

            if ($event->bexio_invoice_id) {
                $invoice = '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i> '.'<a target="_blank" href="https://office.bexio.com/index.php/kb_invoice/show/id/'.$event['bexio_invoice_id'].'">Rechnung</a> ';
                if ($event->contract_status_id === ContractState::RechnungErstellt) {
                    $invoice .= 'erstellt';
                } elseif ($event->contract_status_id === ContractState::RechnungVersendet) {
                    $invoice .= 'erstellt & versendet';
                }
            } else {
                $invoice = '<i class="fa-solid fa-xmark" style="color: indianred"></i> '.'Keine Rechnung erstellt';
            }
            $invoice .= '<br>';
        }

        return $invoice;
    }

    public static function GetChart($time_frame)
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Nur für die Tests (SQLite kennt weder DATE_FORMAT noch Monatsnamen).
            $time_frame_SQL = match ($time_frame) {
                'quarter' => "strftime('%Y', start_date) || '-Q' || ((cast(strftime('%m', start_date) as integer) + 2) / 3) as timeframe",
                'monthly' => "strftime('%m %Y', start_date) as timeframe",
                'daily' => 'start_date as timeframe',
                default => "strftime('%Y', start_date) as timeframe",
            };
        } else {
            switch ($time_frame) {
                case 'quarter':
                    $time_frame_SQL = "concat(DATE_FORMAT(start_date, '%Y'),'-Q', QUARTER(start_date)) as timeframe";
                    break;
                case 'monthly':
                    $time_frame_SQL = "concat(LEFT(DATE_FORMAT(start_date, '%M'),3), ' ', DATE_FORMAT(start_date, '%Y')) as timeframe";
                    break;
                case 'daily':
                    $time_frame_SQL = 'start_date as timeframe';
                    break;
                default:
                    $time_frame_SQL = "DATE_FORMAT(start_date, '%Y') as timeframe";
                    break;
            }
        }

        $events_nights = Event::select(
            DB::raw('sum(total_days) as days'),
            DB::raw('sum(total_people*total_days) as stays'),
            DB::raw($time_frame_SQL),
        )
            ->where('event_status_id', '=', BookingState::Bestaetigt)
            ->groupBy('timeframe')
            ->orderBy('start_date', 'ASC');

        $timeframe = $events_nights->pluck('timeframe');
        $days = $events_nights->pluck('days');
        $stays = $events_nights->pluck('stays');

        $chart[0] = $timeframe;
        $chart[1] = $days;
        $chart[2] = $stays;

        if ($time_frame === 'heatmap') {
            $chart = Helper::GetHeatmap();
        }

        return $chart;
    }

    public static function GetHeatmap()
    {
        $year = Carbon::now()->year - 1;
        $events = Event::where('event_status_id', '=', BookingState::Bestaetigt)->whereYear('start_date', '=', $year)->get();

        $days = collect();

        $formatWeek = fn (int $w) => 'KW '.str_pad($w, 2, '0', STR_PAD_LEFT);

        $weeks = collect(range(1, 53))->map($formatWeek);

        foreach ($events as $event) {
            $period = CarbonPeriod::create(
                Carbon::parse($event->start_date),
                Carbon::parse($event->end_date)
            );

            foreach ($period as $date) {

                $week = $formatWeek($date->isoWeek());
                $weekday = $date->format('N'); // 1–7

                $current = $days->get($weekday, []);

                $current[$week] = ($current[$week] ?? 0) + $event->total_people;
                $days->put($weekday, $current);
            }
        }

        // Reihenfolge Mo–So fixieren
        $weekdayMap = [
            1 => 'Mo',
            2 => 'Di',
            3 => 'Mi',
            4 => 'Do',
            5 => 'Fr',
            6 => 'Sa',
            7 => 'So',
        ];

        $series = collect($weekdayMap)->map(function ($label, $weekday) use ($days, $weeks) {
            $weekData = collect($days->get($weekday, []));

            return [
                'name' => $label,
                'data' => $weeks->map(fn ($week) => [
                    'x' => $week,
                    'y' => $weekData->get($week, 0),
                ])
                    ->values(),
            ];
        })->values();

        return [
            'year' => $year,
            'series' => $series,
        ];
    }

    public static function parseFromMailText(string $text): array
    {
        $labels = ['Anfragenummer', 'Datum', 'Name', 'Gruppenart', 'Anzahl', 'Verpflegung', 'Sprache', 'Kommentar der Gruppe'];

        // Feldnamen zählen nur am Zeilenanfang. Der Kommentar ist das letzte Feld und reicht bis zum Ende,
        // damit er selbst Wörter wie "Anzahl" oder "Name" enthalten darf.
        $extract = function (string $field) use ($text, $labels): ?string {
            $others = array_filter($labels, fn ($l) => $l !== $field);
            // Ein leeres Feld steht allein auf der Zeile, darum endet der Feldname auch am Zeilenende.
            // Beim Kommentar darf der Text auch erst auf der nächsten Zeile beginnen.
            $isComment = $field === 'Kommentar der Gruppe';
            $lookahead = $isComment
                ? '(?=\z)'
                : '(?=\n\h*(?:'.implode('|', array_map(fn ($l) => preg_quote($l, '/'), $others)).')(?:\h|\R|\z)|\z)';
            $pattern = '/^\h*'.preg_quote($field, '/').($isComment ? '\s+' : '\h+').'(.+?)'.$lookahead.'/msu';

            return preg_match($pattern, $text, $m) && trim($m[1]) !== '' ? trim($m[1]) : null;
        };

        preg_match('/(\d{2}\.\d{2}\.\d{4})\s*[\-‐]\s*\w+\s+(\d{2}\.\d{2}\.\d{4})/u', $text, $dates);

        [$firstname, $lastName] = self::splitName($extract('Name') ?? '');
        $number = $extract('Anfragenummer');

        return [
            'buchungsnummer' => $number ? 'A-'.$number : null,
            'mietdauer_von' => isset($dates[1]) ? self::convertDate($dates[1]) : null,
            'mietdauer_bis' => isset($dates[2]) ? self::convertDate($dates[2]) : null,
            'vorname' => $firstname,
            'name' => $lastName,
            'gruppe' => $extract('Gruppenart'),
            'anzahl_personen' => preg_match('/(\d+)/', $extract('Anzahl') ?? '', $n) ? $n[1] : null,
            'verpflegung' => $extract('Verpflegung'),
            'sprache' => $extract('Sprache'),
            'kommentar' => $extract('Kommentar der Gruppe'),
        ];
    }

    public static function parseFromText(string $text): array
    {
        $nameBlock = self::extractMultilineRaw($text, 'Name', 'Gruppe');

        return array_merge(
            self::parseName($nameBlock),
            [
                // \h statt \s: nur Leerzeichen derselben Zeile, sonst nimmt ein leeres Feld die nächste Zeile.
                'gruppe' => self::extract($text, '/Gruppe\h*:\h*(.+)/u'),
                'telefon' => self::extract($text, '/Telefon\h*:\h*(.+)/u'),
                'mobil' => self::extract($text, '/Mobil\h*:\h*(.+)/u'),
                'email' => self::extract($text, '/E[\-‐]Mail\h*:\h*(.+)/u'),
                'mietdauer_von' => self::convertDate(self::extract($text, '/Mietdauer\h*:.*?(\d{2}\.\d{2}\.\d{4})/u')),
                'mietdauer_bis' => self::convertDate(self::extract($text, '/Mietdauer\h*:.*?[\-‐]\h*\w+\h+(\d{2}\.\d{2}\.\d{4})/u')),
                'naechte' => self::extract($text, '/=\h*(\d+)\h*N(?:a|ä)cht/u'),
                'anzahl_personen' => self::extract($text, '/Anzahl Personen\h*:\h*(\d+)/u'),
                'verpflegung' => self::extract($text, '/Verpflegung\h*:\h*(.+)/u'),
            ]
        );
    }

    private static function extract(string $text, string $pattern): ?string
    {
        // Leere Felder als null, damit das Formular seinen Ersatzwert nimmt (z. B. Mobil statt Telefon).
        return preg_match($pattern, $text, $m) && trim($m[1]) !== '' ? trim($m[1]) : null;
    }

    private static function extractMultilineRaw(string $text, string $from, string $until): ?string
    {
        $pattern = '/'.$from.'\s*:\s*(.+?)(?='.$until.'\s*:)/s';

        return preg_match($pattern, $text, $m) ? trim($m[1]) : null;
    }

    private static function convertDate(?string $date): ?string
    {
        return $date === null ? null : Carbon::createFromFormat('d.m.Y', $date)->format('Y-m-d');
    }

    /**
     * "Vorname Nachname" in einem Feld: letztes Wort als Nachname, der Rest als Vorname.
     *
     * @return array{?string, ?string} [Vorname, Nachname]
     */
    private static function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/u', trim($fullName), -1, PREG_SPLIT_NO_EMPTY);
        $lastName = array_pop($parts);

        return [$parts ? implode(' ', $parts) : null, $lastName];
    }

    private static function parseName(string $nameBlock): array
    {
        $lines = array_values(array_filter(array_map('trim', explode("\n", $nameBlock))));
        $nameParts = preg_split('/\s+/u', $lines[0] ?? '', -1, PREG_SPLIT_NO_EMPTY);
        // Anrede nur, wenn das PDF eine angibt; sonst ist das erste Wort schon der Vorname.
        $anrede = in_array($nameParts[0] ?? '', self::SALUTATIONS, true) ? array_shift($nameParts) : '';
        [$vorname, $name] = self::splitName(implode(' ', $nameParts));
        $vorname ??= '';
        $name ??= '';

        preg_match('/^(.+?)\s+(\d+\w*)$/', $lines[1] ?? '', $adresse);
        $strasse = $adresse[1] ?? '';
        $hausnummer = $adresse[2] ?? '';

        preg_match('/(?:CH[\-‐])?(\d{4})\s+(.+)$/u', $lines[2] ?? '', $ort);
        $plz = $ort[1] ?? '';
        $stadt = $ort[2] ?? '';

        return compact('anrede', 'vorname', 'name', 'strasse', 'hausnummer', 'plz', 'stadt');
    }
}
