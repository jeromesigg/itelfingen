<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ixudra\Curl\Facades\Curl;

class ApplicationInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var \App\Models\Application
     */
    protected $application;

    protected $invoice;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Application  $application
     * @return void
     */
    public function __construct(Application $application, $invoice)
    {
        $this->application = $application;
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $application = $this->application;

        $invoice_pdf = Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$this->invoice['id'].'/pdf')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->asJson(true)
            ->get();

        return $this->markdown('emails.applications.invoices', ['application' => $application, 'link' => $this->invoice['network_link']])
            ->to($application['email'], $application['firstname'].' '.$application['name'])
            ->bcc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Deine Rechnung zum Genossenschaftsschein der Genossenschaft Ferienhaus Itelfingen')
            ->attachData(base64_decode($invoice_pdf['content']), 'Rechnung.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
