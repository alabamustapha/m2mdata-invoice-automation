<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Orders</title>
  </head>
  <body>

    <div class="container">
        <h1>Orders</h1>

        <table class="table">
            <thead class="table-light">
                <th>billing_info</th>
                <th>order_id</th>
                <th>customer_id</th>
                <th>status</th>
                <th>date</th>
                <th>date_completed</th>
                <th>total</th>
                <th>line_items</th>
                <th>last_invoice_date</th>
                <th>prorated_days</th>
            </thead>
            <tbody>
                @foreach ($customers_orders as $customer_orders)
                    @foreach($customer_orders as $order)
                        <tr>
                            <td>{{ $order->billing_info["first_name"] . ' ' . $order->billing_info["last_name"] }}</td>
                            <td>{{ $order->order_id }}</td>
                            <td>{{ $order->customer_id }}</td>
                            <td>{{ $order->status }}</td>
                            <td>{{ $order->date }}</td>
                            <td>{{ $order->date_completed }}</td>
                            <td>{{ $order->total }}</td>
                            <td>{{ count($order->line_items) }}</td>
                            <td>{{ $order->last_invoice_date }}</td>
                            <td>{{ $order->prorated_days }}</td>
                        </tr>
                    @endforeach
                @endforeach

            </tbody>
        </table>
    </div>


    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
  </body>
</html>
