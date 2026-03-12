@extends('layouts.app')

@section('content')
<h1>Customer: {{ $customer->name }}</h1>
<ul class="list-unstyled">
    <li><strong>Email:</strong> {{ $customer->email }}</li>
    <li><strong>Phone:</strong> {{ $customer->phone }}</li>
    <li><strong>Address:</strong> {{ $customer->address }}</li>
</ul>
<a href="{{ route('customers.index') }}" class="btn btn-secondary">Back</a>
@endsection
