@extends('layouts.app')

@section('content')
<h1>Transaction</h1>
<ul class="list-unstyled">
    <li><strong>Account:</strong> {{ optional($transaction->account)->name }}</li>
    <li><strong>Amount:</strong> {{ $transaction->amount }}</li>
    <li><strong>Type:</strong> {{ $transaction->type }}</li>
    <li><strong>Date:</strong> {{ $transaction->date }}</li>
    <li><strong>Description:</strong> {{ $transaction->description }}</li>
</ul>
<a href="{{ route('transactions.index') }}" class="btn btn-secondary">Back</a>
@endsection
