<?php

namespace App\Console\Commands;

use App\User;
use App\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\TemplateProcessor;

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

        $date = Carbon::today()->addweeks(2);
        $events = Event::where('last_info', false)->whereNotNull('code')->where('start_date','<=', $date )->where('event_status_id','=', config('status.event_bestaetigt')) ->get();

         foreach($events as $event){
            $this->GetLastInfoPDF($event);
            $event->update(['last_info' => true]);
         }
         $this->info(count($events) . ' Emails versendet.');
    }

    public function GetLastInfoPDF(Event $event)
    {
        $PdfPath = storage_path('app/contracts/Infos_vor_Buchung.pdf');      

        $data["email"] = "jerome.sigg@gmail.com";
        $data["title"] = "Die letzten Infos zu ihrem Aufenthalt.";
        $data["firstname"] = $event['firstname'];
        $data["name"] = $event['name'];
        $data["code"] = $event['code'];
  
        Mail::send('emails.last_infos', $data, function($message)use($data, $PdfPath) {
            $message->to($data["email"], $data['firstname'] . ' ' . $data["name"])
                    ->subject($data["title"])
                    ->attach($PdfPath, [
                        'as'    => 'Infos_vor_Buchung.pdf',
                        'mime'   => 'application/pdf',
                    ]);
        });

    }
}
