<?php

namespace App\Mail;

use App\Models\Application;
use App\Services\BexioApiService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var Application
     */
    protected $application;

    protected $invoice;

    /**
     * Create a new message instance.
     *
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
        $invoice_pdf = app(BexioApiService::class)->get('kb_invoice/'.$application['bexio_invoice_id'].'/pdf');

        // Zahlungsfrist aus der Bexio-Rechnung (is_valid_to), sonst 30 Tage
        $due_date = Carbon::parse($this->invoice['is_valid_to'] ?? now()->addDays(30))->format('d.m.Y');

        return $this->markdown('emails.applications.invoices', ['application' => $application, 'link' => $this->invoice['network_link'], 'due_date' => $due_date])
            ->to($application['email'], $application['firstname'].' '.$application['name'])
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Willkommen in der Genossenschaft Ferienhaus Itelfingen – deine Rechnung für den Genossenschaftsanteil')
            ->attachData(base64_decode($invoice_pdf['content']), 'Rechnung.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
