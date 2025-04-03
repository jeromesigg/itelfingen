<?php

namespace App\View\Components\Bookings;

use Closure;
use App\Models\Event;
use Ixudra\Curl\Facades\Curl;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Entry extends Component
{
    public string $color;
    public string $path;
    public string $title;
    public string $type;
    public string $notification_type;
    public string $fulfilled;
    public $time;
    public $contents;
    public Event $event;
    /**
     * Create a new component instance.
     */
    public function __construct(string $type, Event $event)
    {
        $this->event = $event;
        $this->type = $type;
        $this->contents = [];
        $this->time = null;
        switch ($type) {
            case 'Created':
                $this->fulfilled = $event['contract_status_id'] >= config('status.contract_offen'); 
                $this->color = $this->fulfilled ? "text-green-500 dark:text-green-400" : "text-gray-500 dark:text-gray-400";
                $this->path = "m11.5 11.5 2.071 1.994M4 10h5m11 0h-1.5M12 7V4M7 7V4m10 3V4m-7 13H8v-2l5.227-5.292a1.46 1.46 0 0 1 2.065 2.065L10 17Zm-5 3h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z";
                $this->title = "Buchung erstellt";
                $this->notification_type = "App\Notifications\EventCreatedNotification" ;
                $this->time = $event['created_at'];
                break;
            case 'OfferSend':
                $offer = Curl::to('https://api.bexio.com/2.0/kb_offer/'.$event['bexio_offer_id'])
                    ->withHeader('Accept: application/json')
                    ->withBearer(config('app.bexio_token'))
                    ->get();
                $offer = json_decode($offer, true);
                $this->fulfilled = $event['contract_status_id'] >= config('status.contract_angebot_versendet'); 
                $this->color =$this->fulfilled ? "text-green-500 dark:text-green-400" : "text-gray-500 dark:text-gray-400";
                $this->path = "M11 16v-5.5A3.5 3.5 0 0 0 7.5 7m3.5 9H4v-5.5A3.5 3.5 0 0 1 7.5 7m3.5 9v4M7.5 7H14m0 0V4h2.5M14 7v3m-3.5 6H20v-6a3 3 0 0 0-3-3m-2 9v4m-8-6.5h1";
                $this->title = "Angebot versendet";
                $this->notification_type = "App\Notifications\EventOfferSendNotification" ;
                foreach($event->notifications as $notification) {
                    if($notification['type'] == $this->notification_type) {
                        
                        $this->time = $notification['created_at'];
                        break;
                    }
                }
                if(isset($offer['network_link'])){
                    $this->contents = [[
                        'link'  => $offer['network_link'],
                        'text'  => 'Angebot ansehen',
                        'path' => 'M10 3v4a1 1 0 0 1-1 1H5m8-2h3m-3 3h3m-4 3v6m4-3H8M19 4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1ZM8 12v6h8v-6H8Z'
                    ]];
                }
                break;
            case 'InvoiceCreated':
                $this->fulfilled = $event['contract_status_id'] >= config('status.contract_rechnung_erstellt'); 
                $this->color = $this->fulfilled ? "text-green-500 dark:text-green-400" : "text-gray-500 dark:text-gray-400";
                $this->path = "m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z";
                $this->title = "Buchung definitiv";
                $this->notification_type = "App\Notifications\EventInvoiceCreatedNotification" ;
                foreach($event->notifications as $notification) {
                    if($notification['type'] == $this->notification_type) {
                        
                        $this->time = $notification['created_at'];
                        break;
                    }
                }
                break;

            case 'EventLastInfos':
                $this->fulfilled = $event['last_info']; 
                $this->color = $this->fulfilled ? "text-green-500 dark:text-green-400" : "text-gray-500 dark:text-gray-400";
                $this->path = "M12 5.464V3.099m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175C19 17.4 19 18 18.462 18H5.538C5 18 5 17.4 5 16.807c0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 12 5.464ZM6 5 5 4M4 9H3m15-4 1-1m1 5h1M8.54 18a3.48 3.48 0 0 0 6.92 0H8.54Z";
                $this->title = "Letzte Infos";
                $this->notification_type = "App\Notifications\EventLastInfosNotification" ;
                foreach($event->notifications as $notification) {
                    if($notification['type'] == $this->notification_type) {
                        
                        $this->time = $notification['created_at'];
                        break;
                    }
                }
                $this->contents = [[
                    'link'  => route('bookings.downloadLastInfos', $event['uuid']),
                    'text'  => 'Letzte Infos ansehen',
                ],
                [
                    'link'  =>'#',
                    'text'  => 'Türcode: ' . $event['code'],
                    'path' => 'M10 14v3m4-6V7a3 3 0 1 1 6 0v4M5 11h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1Z',      
                ]];
                
                break;
            case 'EventStay':
                $this->fulfilled = $event['last_info'] && ($event['start_date'] <= now());
                $this->color = $this->fulfilled ? "text-green-500 dark:text-green-400" : "text-gray-500 dark:text-gray-400";
                $this->path = "m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5";
                $this->title = "Aufenthalt im Ferienhaus";
                $this->notification_type = "App\Notifications\EventLastInfosNotification" ;
                $this->time = $event['start_date'];
                $this->contents = [[
                    'link'  => '/faq',
                    'text'  => 'FAQ ansehen',
                    'path' => 'M11 9h6m-6 3h6m-6 3h6M6.996 9h.01m-.01 3h.01m-.01 3h.01M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z'
                ],
                [
                    'link'  => route('bookings.checklist', $event['uuid']),
                    'text'  => 'Checkliste "Hausabgabe" ansehen',
                    'path' => 'M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-6 7 2 2 4-4m-5-9v4h4V3h-4Z',
                ],
                [
                    'link'  =>'#',
                    'text'  => 'Abfallcontainer Code: 4315',
                    'path' => 'M10 14v3m4-6V7a3 3 0 1 1 6 0v4M5 11h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1Z',                  
                ],];
                break;
            case 'Feedback':
                $this->fulfilled = $event['contract_status_id'] >= config('status.contract_rechnung_versendet'); 
                $this->color = $this->fulfilled ? "text-green-500 dark:text-green-400" : "text-gray-500 dark:text-gray-400";
                $this->path = "M10 3v4a1 1 0 0 1-1 1H5m8-2h3m-3 3h3m-4 3v6m4-3H8M19 4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1ZM8 12v6h8v-6H8Z";
                $this->title = "Aufenthalt beendet";
                $this->notification_type = "App\Notifications\EventFeedbackNotification" ;
                $this->time = $event['end_date'];
                $invoice = Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$event['bexio_invoice_id'])
                    ->withHeader('Accept: application/json')
                    ->withBearer(config('app.bexio_token'))
                    ->get();
                $invoice = json_decode($invoice, true);
                $this->contents = [
                [
                    'link'  =>'https://forms.gle/RMWyPzs8wauakQam9',
                    'text'  => 'Feedback abgeben',
                    'path' => 'M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                ]];
                
                if(isset($offer['network_link'])){
                    array_push($this->contents,
                    [
                        'link'  => $invoice['network_link'],
                        'text'  => 'Rechnung ansehen',
                        'path' => 'M10 3v4a1 1 0 0 1-1 1H5m8-2h3m-3 3h3m-4 3v6m4-3H8M19 4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1ZM8 12v6h8v-6H8Z'
                    ]);
                }
                array_push($this->contents,
                    [
                        'link'  =>'/applications',
                        'text'  => 'Genossenschaft beitreten',
                        'path' => 'M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z',
                    ]);            
                break;
            }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.bookings.entry');
    }
}
