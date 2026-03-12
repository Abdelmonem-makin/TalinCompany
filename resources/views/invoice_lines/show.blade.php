@extends('layouts.app')

@section('content')
<h1>Invoice Line</h1>
<ul class="list-unstyled">
    <li><strong>Invoice:</strong> {{ $line->invoice_id ?? $invoiceLine->invoice_id }}</li>
    <li><strong>Item:</strong> {{ optional($invoiceLine->item)->name }}</li>
    <li><strong>Quantity:</strong> {{ $invoiceLine->quantity }}</li>
    <li><strong>Unit price:</strong> {{ $invoiceLine->unit_price }}</li>
    <li><strong>Total:</strong> {{ $invoiceLine->total }}</li>
</ul>
<a href="{{ route('invoice-lines.index') }}" class="btn btn-secondary">Back</a>
@endsection
