<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\DomCrawler\Crawler;
use Codexshaper\WooCommerce\Facades\Customer;
use App\Models\Customer as LaravelCustomer;
use Dcblogdev\Xero\Facades\Xero;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [PageController::class, 'home'])->name('pages.home');
Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
Route::put('customers/{customer}', [CustomerController::class, 'updateXeroId'])->name('customers.update_xero_id');
Route::post('customers/{customer}/send_invoice', [CustomerController::class, 'sendInvoice'])->name('customers.send_invoice');



Route::post('/webhook/order_created', function (Request $request) {

    Log::info("Received");
    Log::info(json_encode($request->all()));

    try {
        $order_data = $request->all();
        $order_date = $order_data['date_created'];
        $date_completed = $order_data['date_completed'];

        $order_date = explode('T', $order_date);
        $order_date = implode(" ", $order_date);

        $date_completed = explode('T', $date_completed);
        $date_completed = implode(" ", $date_completed);


        // $order_date_carbon = Carbon("yyyy-MM-dd HH:mm:ss", $order_date);
        $order_date_carbon = new Carbon($order_date, 'Europe/London');
        $date_completed_carbon = new Carbon($date_completed, 'Europe/London');

        if($order_date_carbon->day < 7){
            $prorated_days = 7 - $order_date_carbon->day; //days to 7th of the current month
            $next_order_date = $order_date_carbon->addDays($prorated_days);
        }else if($order_date_carbon->day > 7){
            $prorated_days = ($order_date_carbon->daysInMonth + 7) - $order_date_carbon->day; //days to 7th of next month
            $next_order_date = $order_date_carbon->addDays($prorated_days);
        }else{
            $prorated_days = 0;
            $next_order_date = $order_date_carbon;
        }

        $order_status = $order_data["status"];
        $order_id = $order_data["id"];
        $customer_id = $order_data["customer_id"];
        $order_total = $order_data["total"];
        $billing_info = $order_data["billing"];


        // Get all line items from order
        $line_items = [];
        foreach($order_data["line_items"] as $line_item){

            $line_item_meta_data = $line_item["meta_data"];

            $line_pool_summary = Arr::first($line_item_meta_data, function ($value, $key) {
                return $value["key"] == "Pool summary";
            });

            $line_pool_summary_table = $line_pool_summary["value"];
            $crawler = new Crawler($line_pool_summary_table);


            $base_mb = $crawler->filter(".sms_plan_cart_table:nth-of-type(1) tbody .sms_plan_tr td:nth-of-type(1)")->text();
            $base_mb_price = $crawler->filter(".sms_plan_cart_table:nth-of-type(1) tbody .sms_plan_tr td:nth-of-type(2)")->text();
            $base_mb_price = (float)ltrim($base_mb_price, '£');


            $total_monthly_mb = $crawler->filter(".sms_plan_cart_table:nth-of-type(2) tbody tr:nth-of-type(2) td:nth-of-type(2)")->text();
            $total_monthly_mb_price = $crawler->filter(".sms_plan_cart_table:nth-of-type(3) tbody tr:nth-of-type(2) td:nth-of-type(4)")->text();
            $total_monthly_mb_price = (float)ltrim($total_monthly_mb_price, '£');



            if($prorated_days > 0){
                $total_prorated_mb = ($total_monthly_mb / 30) * $prorated_days;
                $total_prorated_mb_price = ($total_monthly_mb_price / 30) * $prorated_days;
            }else{
                $total_prorated_mb = 0;
                $total_prorated_mb_price = 0;
            }

            $total_prorated_mb = round($total_prorated_mb, 2);
            $total_prorated_mb_price = round($total_prorated_mb_price, 2);



            $sms_mt_base_price = $crawler->filter(".sms_plan_cart_table:nth-of-type(3) tbody tr:nth-of-type(3) td:nth-of-type(2)")->text();
            $sms_mt_base_price = (float)ltrim($sms_mt_base_price, '£');

            $sms_mt_qty = $crawler->filter(".sms_plan_cart_table:nth-of-type(3) tbody tr:nth-of-type(3) td:nth-of-type(3)")->text();

            $sms_mt_total_price = $crawler->filter(".sms_plan_cart_table:nth-of-type(3) tbody tr:nth-of-type(3) td:nth-of-type(4)")->text();
            $sms_mt_total_price = (float)ltrim($sms_mt_total_price, '£');

            $sms_mt_prorated_qty = ($sms_mt_qty / 30) * $prorated_days;
            $sms_mt_prorated_total_price = ($sms_mt_total_price / 30) * $prorated_days;



            $sms_mo_base_price = $crawler->filter(".sms_plan_cart_table:nth-of-type(3) tbody tr:nth-of-type(4) td:nth-of-type(2)")->text();
            $sms_mo_base_price = (float)ltrim($sms_mo_base_price, '£');

            $sms_mo_qty = $crawler->filter(".sms_plan_cart_table:nth-of-type(3) tbody tr:nth-of-type(4) td:nth-of-type(3)")->text();

            $sms_mo_total_price = $crawler->filter(".sms_plan_cart_table:nth-of-type(3) tbody tr:nth-of-type(4) td:nth-of-type(4)")->text();
            $sms_mo_total_price = (float)ltrim($sms_mo_total_price, '£');

            $sms_mo_prorated_qty = ($sms_mo_qty / 30) * $prorated_days;
            $sms_mo_prorated_total_price = ($sms_mo_total_price / 30) * $prorated_days;



            $summary = [
                "line_id" => $line_item["id"],
                "product_name" => $line_item["name"],
                "product_id" => $line_item["product_id"],
                "variation_id" => $line_item["variation_id"],
                "quantity" => $line_item["quantity"],
                "base_mb" => $base_mb,
                "prorated_days" => $prorated_days,
                "base_mb_price" => $base_mb_price,
                "total_monthly_mb" => $total_monthly_mb,
                "total_monthly_mb_price" => $total_monthly_mb_price,
                "total_prorated_mb" => $total_prorated_mb,
                "total_prorated_mb_price" => $total_prorated_mb_price,
                "sms_mt_base_price" => $sms_mt_base_price,
                "sms_mt_qty" => $sms_mt_qty,
                "sms_mt_total_price" => $sms_mt_total_price,
                "sms_mt_prorated_qty" => $sms_mt_prorated_qty,
                "sms_mt_prorated_total_price" => $sms_mt_prorated_total_price,
                "sms_mo_base_price" => $sms_mo_base_price,
                "sms_mo_qty" => $sms_mo_qty,
                "sms_mo_total_price" => $sms_mo_total_price,
                "sms_mo_prorated_qty" => $sms_mo_prorated_qty,
                "sms_mo_prorated_total_price" => $sms_mo_prorated_total_price
            ];

            array_push($line_items, $summary);
        }


        $order = Order::create([
            "order_id" => $order_id,
            "customer_id" => $customer_id,
            "status" => $order_status,
            "date" => $order_date,
            "date_completed" => $date_completed,
            "total" => $order_total,
            "billing_info" => $billing_info,
            "line_items" => $line_items,
            "last_invoice_date" => null,
            "prorated_days" => $prorated_days,
        ]);

        $customer_id = 203;
        $customer = Customer::find($customer_id);
        $Laravel_customer = LaravelCustomer::firstOrCreate([
            "customer_id" => $customer["id"],
            "date_created" => implode(" ", explode('T', $customer["date_created"])),
            "email" => $customer["email"],
            "first_name" => $customer["first_name"],
            "last_name" => $customer["last_name"],
            "role" => $customer["role"],
            "username" => $customer["username"],
            "billing" => (array)$customer["billing"],
            "shipping" => (array)$customer["shipping"],
            "avatar_url" => $customer["avatar_url"],
        ]);


        $query = 'EmailAddress="' . $customer->email . '"';
        $contact = Xero::contacts()->get(1, $query);
        if(count($contact)){
            $contact = $contact[0];
            $customer->xero_id = $contact["ContactID"];
            $customer->save();
        }
    } catch (Exception $e) {
        Log::info("Something went from wrong");
        Log::error($e->getMessage());
    }

    return true;

});


