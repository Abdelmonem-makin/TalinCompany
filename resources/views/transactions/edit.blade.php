@extends('layouts.app')

@section('content')
<h1>Edit Transaction</h1>
<form method="POST" action="{{ route('transactions.update', $transaction) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Account</label>
        <select name="account_id" class="form-select">
            @foreach($accounts as $id => $name)
                <option value="{{ $id }}" @selected(old('account_id', $transaction->account_id)==$id)>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Amount</label>
        <input name="amount" class="form-control" value="{{ old('amount', $transaction->amount) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Type</label>
        <input name="type" class="form-control" value="{{ old('type', $transaction->type) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" value="{{ old('date', $transaction->date) }}">
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
