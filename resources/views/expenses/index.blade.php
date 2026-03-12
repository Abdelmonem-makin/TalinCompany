@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Expenses</h1>
    <a href="{{ route('expenses.create') }}" class="btn btn-primary">New Expense</a>
</div>

<table class="table table-striped">
    <thead>
        <tr><th>Account</th><th>Amount</th><th>Date</th><th>Category</th><th>Actions</th></tr>
    </thead>
    <tbody>
        @foreach($expenses as $exp)
            <tr>
                <td>{{ optional($exp->account)->name }}</td>
                <td>{{ $exp->amount }}</td>
                <td>{{ $exp->date }}</td>
                <td>{{ $exp->category }}</td>
                <td>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('expenses.show', $exp) }}">View</a>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('expenses.edit', $exp) }}">Edit</a>
                    <form action="{{ route('expenses.destroy', $exp) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $expenses->links() }}
@endsection
