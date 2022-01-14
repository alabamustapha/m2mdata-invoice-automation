<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return "hello";
});


Route::group(['middleware' => ['web']], function(){
    Route::get('xero', function(){



        if (! Xero::isConnected()) {
            return redirect('xero/connect');
        } else {
            //display your tenant name
            var_dump(Xero::getTenantName());

            $contact = Xero::contacts()->get(1, 'EmailAddress="alabamustapha@gmail.com"');

            dd($contact);

            $data = [
                "Type" => "ACCREC",
                "Contact" => [
                  "ContactID" => "cef20e88-8fa4-44b4-a000-916282368f4c"
                ],
                'Reference' => "edrrddddasdazaas",
                "Date" => "2021-12-27T00:00:00",
                "DueDate" => "2021-12-27T00:00:00",
                "DateString" => "2021-12-27T00:00:00",
                "DueDateString" => "2021-12-06T00:00:00",
                "LineAmountTypes" => "Exclusive",
                "LineItems" => [
                  [
                    "Description" => "Consulting services as agreed (20% off standard rate)",
                    "Quantity" => 5,
                    "UnitAmount" => 80.00,
                    "AccountCode" => 200,
                    "DiscountRate" => 10
                  ]
                ]
            ];
            $invoice = Xero::invoices()->store($data);

            dd($invoice);
        }

    });

    Route::get('xero/connect', function(){
        return Xero::connect();
    });
});
