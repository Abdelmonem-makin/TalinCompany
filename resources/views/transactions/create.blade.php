@extends('layouts.app')
@section('content')
<h1>Create Transaction</h1>
<form method="POST" action="{{ route('transactions.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Account</label>
        <select name="account_id" class="form-select">
            @foreach($accounts as $id => $name)
                <option value="{{ $id }}" >{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Amount</label>
        <input name="amount" class="form-control" value="{{ old('amount') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Type</label>
        <input name="type" class="form-control" value="{{ old('type') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" value="{{ old('date') }}">
    </div>
    <button class="btn btn-primary">Create</button>
</form>
@endsection
