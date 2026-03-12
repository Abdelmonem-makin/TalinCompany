@extends('layouts.app')

@section('content')
<h1>Create Account</h1>
<form method="POST" action="{{ route('accounts.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Number</label>
        <input name="number" class="form-control" value="{{ old('number') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Type</label>
        <input name="type" class="form-control" value="{{ old('type') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Balance</label>
        <input name="balance" class="form-control" value="{{ old('balance', 0) }}">
    </div>
    <button class="btn btn-primary">Create</button>
</form>
@endsection
