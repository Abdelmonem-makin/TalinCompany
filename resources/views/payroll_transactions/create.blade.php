@extends('layouts.app')

@section('content')
<h1>Create Payroll Transaction</h1>
<form method="POST" action="{{ route('payroll-transactions.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Employee</label>
        <select name="employee_id" class="form-select">
            @foreach($employees as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Amount</label>
        <input name="amount" class="form-control" value="{{ old('amount') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" value="{{ old('date') }}">
    </div>
    <button class="btn btn-primary">Create</button>
</form>
@endsection
