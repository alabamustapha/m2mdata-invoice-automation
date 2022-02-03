@extends('layouts.app')

@section('content')

    <div class="container">
        <ul>
            <li><a href="{{ route('customers.index') }}">Customers</a></li>
        </ul>
    </div>

@endsection
