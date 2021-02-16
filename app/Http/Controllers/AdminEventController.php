<?php

namespace App\Http\Controllers;

use App\ContractStatus;
use App\Event;
use App\EventStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\TemplateProcessor;

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
            /* Set the PDF Engine Renderer Path */
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');

        /*@ Reading doc file */
        $template = new TemplateProcessor(public_path('contracts/result.docx'));
    
        /*@ Replacing variables in doc file */
        $template->setValue('date', now());
        $template->setValue('firstname', $event['firstname']);
        $template->setValue('lastname',  $event['name']);

        /*@ Save Temporary Word File With New Name */
        $saveDocPath = public_path('contracts/new-result.docx');
        $template->saveAs($saveDocPath);

        // Load temporarily create word file
        $Content = IOFactory::load($saveDocPath); 
    
        return response()->download($saveDocPath);


        //Save it into PDF
        // $savePdfPath = public_path('contracts/new-result.pdf');
        
        // /*@ If already PDF exists then delete it */
        // if ( file_exists($savePdfPath) ) {
        //     unlink($savePdfPath);
        // }

        // //Save it into PDF
        // $PDFWriter = IOFactory::createWriter($Content,'PDF');
        // $PDFWriter->save($savePdfPath); 
        // /*@ Remove temporarily created word file */
        // if ( file_exists($saveDocPath) ) {
        //     unlink($saveDocPath);
        // }

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
        $input = $request->all();
        if($file = $request->file('contract')){
            $name = str_replace(' ', '', $file->getClientOriginalName());
            $file->move('contracts', $name);
            $input['contract'] = $name;
            $input['contract_status_id'] = max($input['contract_status_id'], config('status.contract_versendet'));
        }
        if($file = $request->file('contract_signed')){
            $name = str_replace(' ', '', $file->getClientOriginalName());
            $file->move('contracts/signed', $name);
            $input['contract_signed'] = $name;
            $input['contract_status_id'] = max($input['contract_status_id'], config('status.contract_zurück'));
            $input['event_status_id'] = max($input['event_status_id'], config('status.event_bestaetigt'));
        }

        $event->update($input);
        return redirect('/admin/events');
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
        if($event->contract){
            unlink(public_path() . $event->contract->file);
        }
        if($event->contract_signed){
            unlink(public_path() . $event->contract_signed->file);
        }
        $event->delete();

        return redirect('/admin/events');
    }
}
