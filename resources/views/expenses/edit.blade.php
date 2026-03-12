@extends('layouts.app')

@section('content')
<h1>Edit Expense</h1>
<form method="POST" action="{{ route('expenses.update', $expense) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Account</label>
        <select name="account_id" class="form-select">
            <option value="">--</option>
            @foreach($accounts as $id => $name)
                <option value="{{ $id }}" @selected(old('account_id', $expense->account_id)==$id)>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Amount</label>
        <input name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" value="{{ old('date', $expense->date) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Category</label>
        <input name="category" class="form-control" value="{{ old('category', $expense->category) }}">
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
