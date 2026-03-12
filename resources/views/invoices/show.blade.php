@extends('layouts.app')

@section('content')
<h1>Invoice #{{ $invoice->id }}</h1>
<ul class="list-unstyled">
    <li><strong>Customer:</strong> {{ optional($invoice->customer)->name }}</li>
    <li><strong>Date:</strong> {{ $invoice->date }}</li>
    <li><strong>Due:</strong> {{ $invoice->due_date }}</li>
    <li><strong>Total:</strong> {{ $invoice->total }}</li>
    <li><strong>Status:</strong> {{ $invoice->status }}</li>
</ul>
<a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back</a>
@endsection
