<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\InvoiceDue;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Dcblogdev\Xero\Facades\Xero;
use Illuminate\Support\Facades\Log;

class SendInvoiceToXero
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  App\Events\InvoiceDue  $event
     * @return void
     */
    public function handle(InvoiceDue $event)
    {
        if (! Xero::isConnected()) {
            Log::info("Xero not connected");
        } else {

            Log::info("Xero connected, invoice ebing sent " . Xero::getTenantName());
            Log::info("Xero connected, invoice being sent to" . $event->order->customer->xero_id);

            $data = [
                "Type" => "ACCREC",
                "Contact" => [
                  "ContactID" => $event->order->customer->xero_id,
                ],

                "Reference" => "edrrddddasdazaas",
                "Date" => $event->order->invoice_date,
                "DueDate" => $event->order->invoice_date,
                "DateString" => $event->order->invoice_date,
                "DueDateString" => $event->order->invoice_date,
                "LineAmountTypes" => "Exclusive",
                "LineItems" => $event->order->line_items_summary,
            ];

            $invoice = Xero::invoices()->store($data);
            $event->order->last_invoice_date = new Carbon();
            $event->order->save();

            Log::info("Invoice sent");
        }
    }
}
