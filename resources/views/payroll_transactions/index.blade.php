@extends('layouts.app')

@section('content')
<h1>Payroll Transactions</h1>
<a class="btn btn-primary mb-3" href="{{ route('payroll-transactions.create') }}">New</a>

<table class="table">
    <thead><tr><th>Employee</th><th>Amount</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
        @foreach($rows as $r)
            <tr>
                <td>{{ optional($r->employee)->name }}</td>
                <td>{{ $r->amount }}</td>
                <td>{{ $r->date }}</td>
                <td>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('payroll-transactions.show', $r) }}">View</a>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('payroll-transactions.edit', $r) }}">Edit</a>
                    <form action="{{ route('payroll-transactions.destroy', $r) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $rows->links() }}


@endsection
