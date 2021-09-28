<?php

namespace App\Http\Controllers;

use App\User;
use App\Event;
use App\Homepage;
use Carbon\Carbon;
use App\EventStatus;
use App\ContractStatus;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use PhpOffice\PhpWord\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Spatie\GoogleCalendar\Event as Event_API;

use function PHPUnit\Framework\isNull;

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

        return view('admin.events.index', compact('events', 'event_type', 'events_json'));
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
        $users = User::where('role_id',config('status.role_Team'))->pluck('username','id')->all();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $event_statuses = EventStatus::pluck('name','id')->all();
        $contract_statuses = ContractStatus::pluck('name','id')->all();
        $event = Event::findOrFail($id);
        $users = User::where('role_id',config('status.role_Team'))->pluck('username','id')->all();
        return view('admin.events.edit', compact('event_statuses','event', 'contract_statuses', 'users'));
    }

    public function DownloadContract($id)
    {
        $event = Event::findOrFail($id);
        if($event['contract_status_id'] < config('status.contract_versendet')){
            $event->update(['contract_status_id' => config('status.contract_versendet')]);
        }
        $user = Auth::user();
            /* Set the PDF Engine Renderer Path */
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');

        /*@ Reading doc file */
        $date = Carbon::now()->locale('de_CH')->format('d.m.Y');  
        $start_date_date = Carbon::create($event['start_date'])->locale('de_CH');
        $start_date = $start_date_date->format('d.m.Y');  
        $start_date_file = $start_date_date->format('dm');  
        $end_date_date = Carbon::create($event['end_date'])->locale('de_CH');
        $end_date = $end_date_date->format('d.m.Y'); 
        $end_date_file = $end_date_date->format('dm');  
        $total_other_adults = $event['other_adults'] * $event['total_days'] * config('pricelist.other_adults');
        $total_member_adults =$event['member_adults'] * $event['total_days'] * config('pricelist.member_adults');
        $total_other_kids = $event['other_kids'] * $event['total_days'] * config('pricelist.other_kids');
        $total_member_kids =$event['member_kids'] * $event['total_days'] * config('pricelist.member_kids');

        $template = new TemplateProcessor(storage_path('app/contracts/Mietvertrag.docx'));
    
        /*@ Replacing variables in doc file */
        $template->setValues(array(
            'date_now' => $date,
            'firstname' => $event['firstname'],
            'lastname' =>  $event['name'],
            'street' =>  $event['street'],
            'plz' =>  $event['plz'],
            'city' =>  $event['city'],
            'start_date' =>  $start_date,
            'end_date' =>  $end_date,
            'booking' =>  config('pricelist.booking'),
            'cleaning' => config('pricelist.cleaning'),
            'price_other_adults' => config('pricelist.other_adults'),
            'price_member_adults' => config('pricelist.member_adults'),
            'price_other_kids' => config('pricelist.other_kids'),
            'price_member_kids' => config('pricelist.member_kids'),
            'other_adults' =>  $event['other_adults'],
            'member_adults' =>  $event['member_adults'],
            'other_kids' => $event['other_kids'],
            'member_kids' =>  $event['member_kids'],
            'total_other_adults' =>  $total_other_adults,
            'total_member_adults' => $total_member_adults,
            'total_other_kids' =>  $total_other_kids,
            'total_member_kids' =>  $total_member_kids,
            'total_amount' =>  $event['total_amount'],
            'username' =>  $user['fullname'],
            'user_number' => $user['phone']));
        
        if($user['signature']){
            $template->setImageValue('signature', Storage::url($user['signature']));
        }
        else{
            $template->setValue('signature', '');
        }

        /*@ Save Temporary Word File With New Name */
        $filename =  'Mietvertrag_' . $event['name'] . '_' . $start_date_file . '_' . $end_date_file;
        $saveDocPath = storage_path('app/contracts/' . $filename . '.docx');
        $template->saveAs($saveDocPath);
    
        return response()->download($saveDocPath);
    }

    public function DownloadContractSigned($id)
    {
        $event = Event::findOrFail($id);
        $path = storage_path('app/contracts/signed/'.$event['contract_signed']);
        return response()->download($path);
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
        if($file = $request->file('contract_signed')){ 
            $start_date_file = Carbon::create($event['start_date'])->locale('de_CH')->format('dm');
            $end_date_file = Carbon::create($event['end_date'])->locale('de_CH')->format('dm');
            $name =  'Mietvertrag_' . $event['name'] . '_' . $start_date_file . '_' . $end_date_file;
            $file->storeAs('contracts/signed/', $name);
            $input['contract_signed'] = $name;
            $input['contract_status_id'] = max($input['contract_status_id'], config('status.contract_zurück'));
            $input['event_status_id'] = max($input['event_status_id'], config('status.event_bestaetigt'));
            // Storage::disk('google')->put($name, response()->download(storage_path('app/contracts/signed/'. $name))); 
        }

        return dd(is_null($event['bexio_user_id']));

        if(($event['event_status_id'] == config('status.event_neu')) && ($input['event_status_id'] == config('status.event_bestaetigt'))){
            if(config('app.env') == 'production'){
                $event_api = new Event_API;

                $event_api->name = $event['firstname'] . ' ' . $event['name'] . ' - ' . $event['group_name'];
                $event_api->startDate = Carbon::parse($event->start_date);
                $event_api->endDate = Carbon::parse($event->end_date)->addDay();
                
                $event_api->save();
            }

            
            $this->SendToBexioEx($event);
        }
        $event->update($input);
        return redirect()->back();
    }

    public function SendToBexio($id)
    {
        $event = Event::findOrFail($id);
        $event = $this->SendToBexioEx($event);
        $invoice = Curl::to('https://api.bexio.com/2.0/kb_invoice/' . $event['bexio_invoice_id'] . '/pdf')
        ->withHeader('Accept: application/json')
        ->withBearer(config('app.bexio_token'))
        ->asJson( true )
        ->get(); 

        if(!isset($invoice['error_code'])){
            Storage::disk('local')->put('invoices/Rechnung_' . $event['bexio_invoice_id'] . '.pdf',base64_decode($invoice['content']));
            $path = storage_path('app/invoices/Rechnung_'.$event['bexio_invoice_id'].'.pdf');
            return response()->download($path);
        }
        return redirect()->back();
    }

    public function SendToBexioEx(Event $event){
        if(is_null($event['bexio_user_id'])){
            $query = array(
                array( 
                    'field' => 'name_1',
                    'value' => $event->name
                ),
                array( 
                    'field' => 'name_2',
                    'value' => $event->firstname
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
                $event = $event->update(['bexio_user_id' => $person[0]['id']]);   
            }
        }

        if (is_null($event['bexio_invoice_id'])){
            $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y'); 
            $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y'); 
            $title = "Buchung vom " . $start_date . " bis " . $end_date;
            $positions = array(
                array(
                    'amount' => 1,
                    'type' => 'KbPositionCustom' ,
                    'tax_id' => 16,    
                    'unit_price' => $event->total_amount,
                    'text' => 'Buchung Ferienhaus Itelfingen gemäss Vertrag'
                )
            );
            $invoice = Curl::to('https://api.bexio.com/2.0/kb_invoice')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->withContentType('application/json')
            ->withData( 
                array( 
                    'title' => $title,
                    'contact_id' => $event->bexio_user_id,
                    'user_id' => 1,
                    'is_valid_from' => now(),
                    'is_valid_to' => Carbon::create($event->end_date)->addDays(30),
                    'positions' => $positions
                ) 
            )
            ->asJson(true)
            ->post();

            if(!isset($invoice->error)){
                $event = $event->update(['bexio_invoice_id' => $invoice['id']]);  

                Curl::to('https://api.bexio.com/2.0/kb_invoice/' . $invoice['id'] . '/issue')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->post(); 
            }

        }

        if (is_null($event['bexio_file_id']) && !is_null($event['contract_signed'])){
            $file = Curl::to('https://api.bexio.com/3.0/files ')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->withFile($event['contract_signed'], storage_path('app/contracts/signed/'.$event['contract_signed']))
            ->post();

            $file = json_decode($file, true);

            if(!isset($file->error)){
                $event = $event->update(['bexio_file_id' => $file[0]['id']]);  
            }
        }
        return $event;
    }

    public function SendCleaningMail(Request $request, $id){
        $input = $request->all();
        Mail::raw($input['cleaning_mail_text'],  function($message) use($input){
          $message->to($input['cleaning_mail_address'])->subject('Reiningungsanfrage Ferienhaus Itelfingen');
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
