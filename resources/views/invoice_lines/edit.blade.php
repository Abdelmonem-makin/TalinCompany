@extends('layouts.app')

@section('content')
<h1>Edit Invoice Line</h1>
<form method="POST" action="{{ route('invoice-lines.update', $invoiceLine) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Invoice</label>
        <select name="invoice_id" class="form-select">
            @foreach($invoices as $id => $label)
                <option value="{{ $id }}" @selected(old('invoice_id', $invoiceLine->invoice_id)==$id)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Item</label>
        <select name="item_id" class="form-select">
            @foreach($items as $id => $name)
                <option value="{{ $id }}" @selected(old('item_id', $invoiceLine->item_id)==$id)>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Quantity</label>
        <input name="quantity" class="form-control" value="{{ old('quantity', $invoiceLine->quantity) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Unit price</label>
        <input name="unit_price" class="form-control" value="{{ old('unit_price', $invoiceLine->unit_price) }}">
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
