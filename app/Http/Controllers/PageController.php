<?php

namespace App\Http\Controllers;

use App\Events\InvoiceDue;
use App\Models\Order;
use Dcblogdev\Xero\Resources\Invoices;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home(){
        return view('home');
    }
}
