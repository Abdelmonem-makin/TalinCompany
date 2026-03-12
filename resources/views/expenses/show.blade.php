@extends('layouts.app')

@section('content')
<h1>Expense</h1>
<ul class="list-unstyled">
    <li><strong>Account:</strong> {{ optional($expense->account)->name }}</li>
    <li><strong>Amount:</strong> {{ $expense->amount }}</li>
    <li><strong>Date:</strong> {{ $expense->date }}</li>
    <li><strong>Category:</strong> {{ $expense->category }}</li>
    <li><strong>Description:</strong> {{ $expense->description }}</li>
</ul>
<a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
@endsection
