@extends('layouts.app')

@section('content')
<h1>Edit Employee</h1>
<form method="POST" action="{{ route('employees.update', $employee) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name', $employee->name) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" class="form-control" value="{{ old('email', $employee->email) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Phone</label>
        <input name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Position</label>
        <input name="position" class="form-control" value="{{ old('position', $employee->position) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Salary</label>
        <input name="salary" class="form-control" value="{{ old('salary', $employee->salary) }}">
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
