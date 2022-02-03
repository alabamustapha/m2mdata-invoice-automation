<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\InvoiceDue;
use Dcblogdev\Xero\Facades\Xero;
use Illuminate\Queue\InteractsWithQueue;
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
                "Date" => "2021-12-27T00:00:00",
                "DueDate" => "2021-12-27T00:00:00",
                "DateString" => "2021-12-27T00:00:00",
                "DueDateString" => "2021-12-06T00:00:00",
                "LineAmountTypes" => "Exclusive",
                "LineItems" => $event->order->line_items_summary,
            ];

            $invoice = Xero::invoices()->store($data);

            Log::info("Invoice sent");
        }
    }
}
