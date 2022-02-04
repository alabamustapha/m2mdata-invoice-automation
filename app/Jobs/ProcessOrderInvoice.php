<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Carbon\Carbon;
use Dcblogdev\Xero\Facades\Xero;
use Illuminate\Support\Facades\Log;

class ProcessOrderInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     *The Order Instance
     *
     * @var App\Models\Order
     *
     */

     protected $order;

      /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 86400;

     /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Log::info("Xero connected, invoice ebing sent " . Xero::getTenantName());
        Log::info("Xero connected, invoice being sent to" . $this->order->customer->xero_id);

        $data = [
            "Type" => "ACCREC",
            "Contact" => [
                "ContactID" => $this->order->customer->xero_id,
            ],

            "Reference" => "M2MAUTOMATED-" . rand(0,300000),
            "Date" => $this->order->invoice_date,
            "DueDate" => $this->order->invoice_date,
            "DateString" => $this->order->invoice_date,
            "DueDateString" => $this->order->invoice_date,
            "LineAmountTypes" => "Exclusive",
            "LineItems" => $this->order->line_items_summary,
        ];

        $invoice = Xero::invoices()->store($data);
        $this->order->last_invoice_date = new Carbon();
        $this->order->save();

        Log::info("Invoice sent");

    }

}
