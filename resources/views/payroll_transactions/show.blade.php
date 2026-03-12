@extends('layouts.app')

@section('content')
<h1>Payroll Transaction</h1>
<ul class="list-unstyled">
    <li><strong>Employee:</strong> {{ optional($payrollTransaction->employee)->name }}</li>
    <li><strong>Amount:</strong> {{ $payrollTransaction->amount }}</li>
    <li><strong>Date:</strong> {{ $payrollTransaction->date }}</li>
    <li><strong>Description:</strong> {{ $payrollTransaction->description }}</li>
</ul>
<a href="{{ route('payroll-transactions.index') }}" class="btn btn-secondary">Back</a>
@endsection
