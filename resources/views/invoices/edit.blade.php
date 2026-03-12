@extends('layouts.app')

@section('content')
<h1>Edit Invoice</h1>
<form method="POST" action="{{ route('invoices.update', $invoice) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Customer</label>
        <select name="customer_id" class="form-select">
            @foreach($customers as $id => $name)
                <option value="{{ $id }}" @selected(old('customer_id', $invoice->customer_id)==$id)>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" value="{{ old('date', $invoice->date) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $invoice->due_date) }}">
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
