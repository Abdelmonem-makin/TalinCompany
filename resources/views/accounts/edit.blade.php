@extends('layouts.app')

@section('content')
<h1>Edit Account</h1>
<form method="POST" action="{{ route('accounts.update', $account) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name', $account->name) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Number</label>
        <input name="number" class="form-control" value="{{ old('number', $account->number) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Type</label>
        <input name="type" class="form-control" value="{{ old('type', $account->type) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Balance</label>
        <input name="balance" class="form-control" value="{{ old('balance', $account->balance) }}">
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
