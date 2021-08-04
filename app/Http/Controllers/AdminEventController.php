<?php

namespace App\Http\Controllers;

use App\Event;
use Carbon\Carbon;
use App\EventStatus;
use App\ContractStatus;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
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
        $event_statuses = EventStatus::pluck('name','id')->all();
        return view('admin.events.create', compact('event_statuses'));
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
        return view('admin.events.edit', compact('event_statuses','event', 'contract_statuses'));
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
        $date_file = Carbon::now()->locale('de_CH')->format('ymd');  
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
        
        $template->setImageValue('signature', Storage::url($user['signature']));

        /*@ Save Temporary Word File With New Name */
        $filename =  $date_file . '_Mietvertrag_' . $event['name'] . '_' . $start_date_file . '_' . $end_date_file;
        $saveDocPath = public_path('contracts/' . $filename . '.docx');
        $template->saveAs($saveDocPath);

        // Load temporarily create word file
        $Content = IOFactory::load($saveDocPath); 
    
        return response()->download($saveDocPath);


        //Save it into PDF
        $savePdfPath = public_path('contracts/' . $filename . '.pdf');
        
        /*@ If already PDF exists then delete it */
        if ( file_exists($savePdfPath) ) {
            unlink($savePdfPath);
        }

        //Save it into PDF
        $PDFWriter = IOFactory::createWriter($Content,'PDF');
        $PDFWriter->save($savePdfPath); 
        /*@ Remove temporarily created word file */
        if ( file_exists($saveDocPath) ) {
            unlink($saveDocPath);
        }

        return response()->download($savePdfPath);

        // $data["email"] = "jerome.sigg@gmail.com";
        // $data["title"] = "From ItSolutionStuff.com";
        // $data["body"] = "This is Demo";
  
        // Mail::send('emails.myTestMail', $data, function($message)use($data, $savePdfPath) {
        //     $message->to($data["email"], $data["email"])
        //             ->subject($data["title"])
        //             ->attach($savePdfPath, [
        //                 'as'    => 'Vertrag.pdf',
        //                 'mime'   => 'application/pdf',
        //             ]);
        // });
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
            $name = str_replace(' ', '', $file->getClientOriginalName());
            $file->move('contracts/signed', $name);
            $input['contract_signed'] = $name;
            $input['contract_status_id'] = max($input['contract_status_id'], config('status.contract_zurück'));
            $input['event_status_id'] = max($input['event_status_id'], config('status.event_bestaetigt'));
            Storage::disk('google')->put($name, response()->download(public_path().'/contracts/signed/'. $name)); 
        }

        if(($event['event_status_id'] == config('status.event_neu')) && ($input['event_status_id'] == config('status.event_bestaetigt'))){
            $event_api = new Event_API;

            $event_api->name = $event->event_status['name'] . ' - ' . $name . ' - ' . $event['group_name'];
            $event_api->startDate = Carbon::parse($event->start_date);
            $event_api->endDate = Carbon::parse($event->end_date);
            
            $event_api->save();

            AdminEventController::SendToBexio($event->id);
        }
        $event->update($input);
        return redirect()->back();
    }

    static function SendToBexio($id)
    {
        $event = Event::findOrFail($id);
        if (!$event['bexio_user_id']){
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
                    ->enableDebug('C:\Temp\logFile.txt')
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
               $event = Event::findOrFail($id);
            }
        }

        if (!$event['bexio_invoice_id']){
            $title = "Buchung vom " . $event->start_date . " bis " . $event->end_date;
            $positions = array(
                array(
                    'amount' => 1,
                    'type' => 'KbPositionCustom' ,
                    'tax_id' => 16,    
                    'unit_price' => $event->total_amount,
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
            }

            Curl::to('https://api.bexio.com/2.0/kb_invoice/' . $invoice['id'] . '/issue')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->post();
        }

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
