@extends('layouts.app')

@section('content')
<h1>Create Expense</h1>
<form method="POST" action="{{ route('expenses.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Account</label>
        <select name="account_id" class="form-select">
            <option value="">--</option>
            @foreach($accounts as $id => $name)
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
    <div class="mb-3">
        <label class="form-label">Category</label>
        <input name="category" class="form-control" value="{{ old('category') }}">
    </div>
    <button class="btn btn-primary">Create</button>
</form>
@endsection
