<?php

namespace App\Http\Controllers;

use App\User;
use App\Event;
use App\Homepage;
use App\Position;
use Carbon\Carbon;
use App\EventStatus;
use App\Helper\Helper;
use App\ContractStatus;
use App\PricelistPosition;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\isNull;
use Illuminate\Support\Facades\Storage;
use Spatie\GoogleCalendar\Event as Event_API;

class AdminEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $events = Event::where('end_date','>',Carbon::today())->orderBy('start_date')->paginate(5);
        $event_type = 'admin';
        $events_all = Event::all();
        $positions = PricelistPosition::where([['show', true],['archive_status_id', config('status.aktiv')]])->orderby('bexio_code')->get();
        
        $events_json = [];
        foreach ($events_all as $event)
        {
            $start_date = new Carbon($event['start_date']);
            $end_date = new Carbon($event['end_date']);
            $start = [
                'y' => $start_date->year,
                'm' => $start_date->month-1,
                'd' => $start_date->day,
                'h' => true,
            ];
            $end = [
                'y' => $end_date->year,
                'm' => $end_date->month-1,
                'd' => $end_date->day,
                'h' => true,
            ];
            $events_json[] = [
                'start' => $start,
                'end' => $end,
                'state' => $event->event_status['color'],
                'id' => $event->id
            ];
        }

        return view('admin.events.index', compact('events', 'event_type', 'events_json', 'positions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $homepages = Homepage::all();
        $event_statuses = EventStatus::pluck('name','id')->all();
        $users = User::where('role_id',config('status.role_Verwalter'))->pluck('username','id')->all();
        return view('admin.events.create', compact('event_statuses', 'homepages', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        $input['contract_status_id'] = config('status.contract_offen');
        Event::create($input);
        return redirect('/admin/events');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $positions = Position::with('pricelist_position')
            ->where('event_id',$id)
            ->orderBy(PricelistPosition::select('bexio_code')
            ->whereColumn('pricelist_positions.id', 'positions.pricelist_position_id')
            )->get();
        
        $event_statuses = EventStatus::pluck('name','id')->all();
        $contract_statuses = ContractStatus::pluck('name','id')->all();
        $event = Event::findOrFail($id);
        $users = User::where('role_id',config('status.role_Verwalter'))->pluck('username','id')->all();
        return view('admin.events.edit', compact('event_statuses','event', 'contract_statuses', 'users', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $event = Event::findOrFail($id);
        $input = $request->all();

        $plpositions = PricelistPosition::where([['archive_status_id', config('status.aktiv')]])->
            where([['bexio_code', '<', 300]])->orderby('bexio_code')->get();
        foreach($plpositions as $index => $plposition){
            Helper::CreateRePos($input['positions'][$index], $plposition['id'], $event);
        }
        $event->update($input);
        return redirect()->back();
    }

    public function CreateOffer($id)
    {
        $event = Event::findOrFail($id);
        $event = $this->CreateContact($event);
        if (is_null($event['bexio_offer_id'])){

            $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y'); 
            $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y'); 
            $title = "Buchung vom " . $start_date . " bis " . $end_date;
            $positions = Position::with('pricelist_position')->where('event_id',$id)->get()->sortBy('pricelist_position.bexio_code');
            $positions_array = [];
            foreach($positions as $position){
                $amount = $position->pricelist_position['bexio_code'] > 200 ? max($position['amount']-3,0) : $position['amount'];
                if($amount > 0){
                    array_push($positions_array,
                        array(
                            'amount' => $position->pricelist_position['bexio_code'] < 100 ? $amount : $event['total_days'] * $amount,
                            'type' => 'KbPositionArticle' ,
                            'tax_id' => 16,    
                            'article_id' => $position->pricelist_position['bexio_id'],
                            'unit_price' => $position->pricelist_position['price'],
                            'discount_in_percent' => $event['discount'],
                        )
                    );
                }
            }
            $offer = Curl::to('https://api.bexio.com/2.0/kb_offer')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->withContentType('application/json')
                ->withData( 
                    array( 
                        'title' => $title,
                        'contact_id' => $event->bexio_user_id,
                        'user_id' => 1,
                        'is_valid_from' => now(),
                        'is_valid_until' => Carbon::create($event->start_date)->addDays(-14),
                        'api_reference' => $event['id'],
                        'positions' => $positions_array
                    ) 
                )
                ->asJson(true)
                ->post();

            if(!isset($offer['error_code'])){
                $event->update(['bexio_offer_id' => $offer['id'],
                'contract_status_id' => config('status.contract_angebot_erstellt')]);  
                if(config('mail.direct_send')){
                    $this->SendOfferEx($event);
                }
                
            }
        }
        return redirect()->back();
    }

    public function SendOffer($id)
    {
        $event = Event::findOrFail($id);
        if (!is_null($event['bexio_offer_id'])){
            $event = $this->SendOfferEx($event);
        }
        return redirect()->back();
    }

    public function SendOfferEx(Event $event)
    {
        if (!is_null($event['bexio_offer_id'])){

            $offer = Curl::to('https://api.bexio.com/2.0/kb_offer/' . $event['bexio_offer_id'])
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->get(); 
            $offer = json_decode($offer, true);

            $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y'); 
            $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y'); 
            $title = "Buchung vom " . $start_date . " bis " . $end_date;
            Curl::to('https://api.bexio.com/2.0/kb_offer/' . $event['bexio_offer_id'] . '/issue')
                    ->withHeader('Accept: application/json')
                    ->withBearer(config('app.bexio_token'))
                    ->post(); 

            $message = 'Guten Tag ' . $event['firstname'] . ' ' . $event['name'] .',
            
            Vielen Dank für Dein Interesse an das Ferienhaus Itelfingen und Deine ' . $title . '.
            
            Unter folgendem Link kannst Du deine Buchung für Deinen Aufenthalt vom ' . $start_date . ' über CHF ' . $offer['total'] .  ' ansehen:
            [Network Link]
            
            Wir hoffen, dass Die Buchung Deinen Wünschen entspricht und würden uns über Deine Bestätigung freuen. Die Bestätigung beinhaltet ebenfalls ein Akzeptieren der Hausordnung im angehängten PDF.
            Für Rückfragen und weitere Informationen stehen wir gerne jederzeit zur Verfügung.
            
            Freundliche Grüsse,
            Das Ferienhaus Itelfingen';

            Curl::to('https://api.bexio.com/2.0/kb_offer/' . $offer['id'] . '/send')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->withData( 
                    array( 
                        'recipient_email' => $event['email'],
                        'subject' => $title,
                        'message' => $message,
                        'mark_as_open' => true
                    ) 
                )
                ->asJson(true)
                ->post(); 
            $event->update(['contract_status_id' => config('status.contract_angebot_versendet')]);  
        }
        return true;

    }

    public function CreateInvoice($id)
    {
        $event = Event::findOrFail($id);
        $event = $this->CreateContact($event);
        if (is_null($event['bexio_invoice_id']) && !is_null($event['bexio_offer_id'])){
            $invoice = Curl::to('https://api.bexio.com/2.0/kb_offer/' . $event['bexio_offer_id'] . '/invoice')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->post(); 

            $invoice = json_decode($invoice, true);
            if(!isset($invoice['error_code'])){ 
                $event->update([
                    'bexio_invoice_id' => $invoice['id']]); 
                Curl::to('https://api.bexio.com/2.0/kb_invoice/' . $invoice['id'] . '/issue')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->post(); 

            }
        }
        else{
            $invoice = Curl::to('https://api.bexio.com/2.0/kb_invoice/' . $event['bexio_invoice_id'])
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->get();
            $invoice = json_decode($invoice, true);

        }
        if(isset($invoice['id'])){

            $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y'); 
            $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y'); 
            $title = "Deine Rechnung zur Buchung vom " . $start_date . " bis " . $end_date;

            $message = 'Guten Tag ' . $event['firstname'] . ' ' . $event['name'] .',
            
            Vielen Dank für die Bestätigung Deiner Buchung für das Ferienhaus Itelfingen.
            
            Unter folgendem Link kannst Du Deine Rechnung für Deinen Aufenthalt vom ' . $start_date . ' über CHF ' . $invoice['total'] .  ' ansehen:
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
                        'recipient_email' => $event['email'],
                        'subject' => $title,
                        'message' => $message,
                        'mark_as_open' => true
                    ) 
                )
                ->asJson(true)
                ->post(); 
            
            $invoice_pdf = Curl::to('https://api.bexio.com/2.0/kb_invoice/' . $event['bexio_invoice_id'] . '/pdf')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->asJson( true )
                ->get(); 
    
            if(!isset($invoice_pdf['error_code'])){
                $start_date_file = Carbon::create($event['start_date'])->locale('de_CH')->format('dm');
                $end_date_file = Carbon::create($event['end_date'])->locale('de_CH')->format('dm');
                $name_pdf =  'Rechnung_' . $event['name'] . '_' . $start_date_file . '_' . $end_date_file;
                Storage::disk('google')->put($name_pdf,base64_decode($invoice_pdf['content']));
      
            }

            if(config('app.env') == 'production'){
                $event_api = new Event_API;
                $event_api->name = $event['firstname'] . ' ' . $event['name'] . ' - ' . $event['group_name'] . ' - ' . $event['telephone'];
                $event_api->startDate = Carbon::parse($event->start_date);
                $event_api->endDate = Carbon::parse($event->end_date)->addDay();    
                $event_api->save();
            }
            $event->update([
                'event_status_id' => config('status.event_bestaetigt'),
                'contract_status_id' => config('status.contract_rechnung_gestellt')]); 
        }
        
        return redirect()->back();
    }

    public function CreateContact(Event $event)
    {
        if(is_null($event['bexio_user_id'])){
            $query = array(
                array( 
                    'field' => 'name_1',
                    'value' => $event->name
                ),
                array( 
                    'field' => 'name_2',
                    'value' => $event->firstname ? $event->firstname  : ''
                ),
                array( 
                    'field' => 'address',
                    'value' => $event->street
                ),
                array( 
                    'field' => 'postcode',
                    'value' => $event->plz
                ),);
            $person = Curl::to('https://api.bexio.com/2.0/contact/search')
                    ->withHeader('Accept: application/json')
                    ->withBearer(config('app.bexio_token'))
                    ->withContentType('application/json')
                    ->withData($query)
                    ->asJson(true)
                    ->post();

            if(count($person) === 0){
                $person = Curl::to('https://api.bexio.com/2.0/contact')
                    ->withHeader('Accept: application/json')
                    ->withBearer(config('app.bexio_token'))
                    ->withContentType('application/json')
                    ->withData( array( 
                        'contact_type_id' => '2',
                        'name_1' => $event->name,
                        'name_2' => $event->firstname,
                        'address' => $event->street,
                        'postcode' => $event->plz,
                        'city' => $event->city,
                        'country_id' => 1,
                        'mail' => $event->email,
                        'phone_mobile' => $event->telephone,
                        'remarks' => $event->comment,
                        'user_id' => 1,
                        'owner_id' => 1,
                        ) )
                    ->asJson(true)
                    ->post();
            }  
        
            if(!isset($person->error)){
                $event->update(['bexio_user_id' => $person[0]['id']]);   
            }
        }
        return $event;
    }

    public function SendCleaningMail(Request $request, $id){
        $input = $request->all();
        Mail::raw($input['cleaning_mail_text'],  function($message) use($input){
          $message->to($input['cleaning_mail_address'])->bcc(config('mail.from.address'), config('mail.from.name'))->subject('Reiningungsanfrage Ferienhaus Itelfingen');
        });
        $event = Event::findOrFail($id);
        $event->update(['cleaning_mail' => true]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $event = Event::findOrFail($id);
        if($event->contract_signed){
            unlink(public_path() . $event->contract_signed->file);
        }
        $event->delete();

        return redirect('/admin/events');
    }
}
