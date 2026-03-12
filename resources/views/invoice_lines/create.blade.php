@extends('layouts.app')

@section('content')
<h1>Create Invoice Line</h1>
<form method="POST" action="{{ route('invoice-lines.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Invoice</label>
        <select name="invoice_id" class="form-select">
            @foreach($invoices as $id => $label)
                <option value="{{ $id }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Item</label>
        <select name="item_id" class="form-select">
            @foreach($items as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Quantity</label>
        <input name="quantity" class="form-control" value="1">
    </div>
    <div class="mb-3">
        <label class="form-label">Unit price</label>
        <input name="unit_price" class="form-control" value="0">
    </div>
    <button class="btn btn-primary">Create</button>
</form>
@endsection
