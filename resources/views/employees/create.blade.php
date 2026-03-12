@extends('layouts.app')

@section('content')
<h1>Create Employee</h1>
<form method="POST" action="{{ route('employees.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" class="form-control" value="{{ old('email') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Phone</label>
        <input name="phone" class="form-control" value="{{ old('phone') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Position</label>
        <input name="position" class="form-control" value="{{ old('position') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Salary</label>
        <input name="salary" class="form-control" value="{{ old('salary', 0) }}">
    </div>
    <button class="btn btn-primary">Create</button>
</form>
@endsection
