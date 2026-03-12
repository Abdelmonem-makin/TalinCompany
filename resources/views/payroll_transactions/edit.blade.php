@extends('layouts.app')

@section('content')
<h1>Edit Payroll Transaction</h1>
<form method="POST" action="{{ route('payroll-transactions.update', $payrollTransaction) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Employee</label>
        <select name="employee_id" class="form-select">
            @foreach($employees as $id => $name)
                <option value="{{ $id }}" @selected(old('employee_id', $payrollTransaction->employee_id)==$id)>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Amount</label>
        <input name="amount" class="form-control" value="{{ old('amount', $payrollTransaction->amount) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" value="{{ old('date', $payrollTransaction->date) }}">
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
