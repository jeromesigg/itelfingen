<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\PricelistPosition;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ixudra\Curl\Facades\Curl;
use Revolution\Google\Sheets\Facades\Sheets;

class ApplicationInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var \App\Models\Application
     */
    protected $application;
    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Application  $application
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $application = $this->application;

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


        }
        return $this->markdown('emails.applications.invoice');
    }
}
