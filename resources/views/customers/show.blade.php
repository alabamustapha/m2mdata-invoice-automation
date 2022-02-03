@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <h1>{{ $customer->first_name . ' ' . $customer->last_name }} - {{ $customer->email }}</h1>


    <div class="container">
        <div class="row justify-content-start">
          <div class="col-4">
            <h5>Billing info</h5>
            <p>
                {!! implode("<br>", $customer->billing) !!}
            </p>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#nextInvoiceModal">
                View next invoice
            </button>


            <form action="{{ route("customers.send_invoice", $customer->id) }}" method="POST" id="sendInvoiceForm" class="d-none">
                @csrf
            </form>

            <button type="submit" class="btn btn-primary" onclick="
                document.querySelector('form#sendInvoiceForm').submit();
            ">Send due invoice</button>

            <!-- Modal -->
            <div class="modal fade" id="nextInvoiceModal" tabindex="-1" aria-labelledby="nextInvoiceModal" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="nextInvoiceModallLabel">Next Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <th>Item description</th>
                                <th>Unit Price</th>
                                <th>Qty</th>
                                <th>Line Price</th>
                            </thead>
                            <tbody>
                                @foreach ($customer->orders as $order)
                                    @foreach ($order->line_items_summary as $line_item)
                                    <tr>
                                        <td>{{ $line_item["Description"] }}</td>
                                        <td>{{ $line_item["UnitAmount"] }}</td>
                                        <td>{{ $line_item["Quantity"] }}</td>
                                        <td>{{ $line_item["UnitAmount"] * $line_item["Quantity"] }}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
                </div>
            </div>
          </div>
        </div>
    </div>



    <hr>

    <h2>Orders History</h2>

    <table class="table">
        <thead class="table-light">
            <th>Order ID</th>
            <th>Status</th>
            <th>Date</th>
            <th>Date completed</th>
            <th>total</th>
            <th>Line items</th>
            <th>Last invoiced date</th>
            <th>Next invoice</th>
            <th>Prorated days</th>
        </thead>
        <tbody>
            @foreach ($customer->orders as $order)
            <tr>
                <td>{{ $order->order_id }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->date }}</td>
                <td>{{ $order->date_completed }}</td>
                <td>{{ $order->total }}</td>
                <td>{{ count($order->line_items) }}</td>
                <td>{{ $order->last_invoice_date }}</td>
                <td>{{ $order->invoice_date }}</td>
                <td>{{ $order->prorated_days }}</td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>


@endsection
