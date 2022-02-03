@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <h1>Customers</h1>

    <table class="table">
        <thead class="table-light">
            <th>ID</th>
            <th>Name</th>
            <th>WooUsername</th>
            <th>Email</th>
            <th>Orders count</th>
            <th>Xero ID</th>
            <th>Actions</th>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td>{{ $customer->customer_id }}</td>
                    <td>{{ $customer->first_name . ' ' . $customer->last_name }}</td>
                    <td>{{ $customer->username }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->orders->count() }}</td>
                    <td>
                        @if ($customer->xero_id)
                            {{ $customer->xero_id }}
                        @else

                            <form action="{{ route('customers.update_xero_id', $customer->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button>Submit</button>
                            </form>
                        @endif
                    </td>
                    <td><a href="{{ route('customers.show', $customer->id) }}" class="btn btn-primary btn-sm">View</a></td>

                </tr>
            @endforeach

        </tbody>
    </table>
</div>


@endsection
