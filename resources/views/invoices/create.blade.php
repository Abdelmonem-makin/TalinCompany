@extends('layouts.app')

@section('content')
<h1>Create Invoice</h1>
<form method="POST" action="{{ route('invoices.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Customer</label>
        <select name="customer_id" class="form-select">
            @foreach($customers as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" value="{{ old('date') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
    </div>
    <button class="btn btn-primary">Create</button>
</form>
@endsection
