<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ["order_id", "prorated_days","customer_id","status","date","date_completed","total","billing_info","line_items","last_invoice_date"];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'line_items' => 'array',
        'billing_info' => 'array',
        'date' => 'datetime:Y-m-d H:i:s',
        'date_completed' => 'datetime:Y-m-d H:i:s',
        'last_invoice_date' => 'datetime:Y-m-d H:i:s'
    ];

    public function getLineItemsSummaryAttribute(){
        $line_items = [];

        foreach($this->line_items as $line_item){
            if($line_item["prorated_days"] > 0 && !$this->is_prorated){
                $summary["Description"] = "Prorated mb: " . $line_item["total_prorated_mb"];
                $summary["UnitAmount"] = round($line_item["total_prorated_mb_price"] / $line_item["quantity"], 2);
                $summary["Quantity"] = (int)$line_item["quantity"];
                $summary["AccountCode"] = 200;
                $summary["DiscountRate"] = 0;
                array_push($line_items, $summary);

                $summary["Description"] = "Prorated SMS MO";
                $summary["UnitAmount"] = $line_item["sms_mo_base_price"];
                $summary["Quantity"] = (int)ceil($line_item["sms_mo_prorated_qty"]);
                $summary["AccountCode"] = 200;
                $summary["DiscountRate"] = 0;
                array_push($line_items, $summary);

                $summary["Description"] = "Prorated SMS MT";
                $summary["UnitAmount"] = $line_item["sms_mt_base_price"];
                $summary["Quantity"] = (int)ceil($line_item["sms_mt_prorated_qty"]);
                $summary["AccountCode"] = 200;
                $summary["DiscountRate"] = 0;
                array_push($line_items, $summary);
            }

            $summary["Description"] = "Data Plan MB: " . $line_item["base_mb"];
            $summary["UnitAmount"] = $line_item["base_mb_price"];
            $summary["Quantity"] = $line_item["quantity"];
            $summary["AccountCode"] = 200;
            $summary["DiscountRate"] = 0;
            array_push($line_items, $summary);

            $summary["Description"] = "SMS MO: " . $line_item["sms_mo_qty"];
            $summary["UnitAmount"] = $line_item["sms_mo_base_price"];
            $summary["Quantity"] = $line_item["sms_mt_qty"];
            $summary["AccountCode"] = 200;
            $summary["DiscountRate"] = 0;
            array_push($line_items, $summary);

            $summary["Description"] = "SMS MT: " . $line_item["sms_mt_qty"];
            $summary["UnitAmount"] = $line_item["sms_mt_base_price"];
            $summary["Quantity"] = $line_item["sms_mt_qty"];
            $summary["AccountCode"] = 200;
            $summary["DiscountRate"] = 0;
            array_push($line_items, $summary);

        }

        return $line_items;
    }

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function getIsProratedAttribute(){
        return false;
    }

}
