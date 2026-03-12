@extends('layouts.app')

@section('content')
<h1>Supplier: {{ $supplier->name }}</h1>
<ul class="list-unstyled">
    <li><strong>Email:</strong> {{ $supplier->email }}</li>
    <li><strong>Phone:</strong> {{ $supplier->phone }}</li>
    <li><strong>Address:</strong> {{ $supplier->address }}</li>
</ul>
<a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back</a>
@endsection
