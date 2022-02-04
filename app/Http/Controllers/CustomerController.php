<?php

namespace App\Http\Controllers;

use App\Events\InvoiceDue;
use App\Jobs\ProcessOrderInvoice;
use App\Models\Customer;
use App\Models\Order;
use Dcblogdev\Xero\Facades\Xero;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(){

        $customers = Customer::with('orders')->get();
        return view("customers.index", compact('customers'));
    }

    public function updateXeroId(Customer $customer){


        $query = 'EmailAddress="' . $customer->email . '"';
        $contact = Xero::contacts()->get(1, $query);
        if(count($contact)){
            $contact = $contact[0];
            $customer->xero_id = $contact["ContactID"];
            $customer->save();
        }

        return redirect()->back();

    }

    public function sendInvoice(Customer $customer){
        foreach($customer->orders as $order){
            ProcessOrderInvoice::dispatchSync($order);
        }
        return redirect()->back();
    }

    public function show(Customer $customer){

        return view('customers.show', compact('customer'));
    }
}
