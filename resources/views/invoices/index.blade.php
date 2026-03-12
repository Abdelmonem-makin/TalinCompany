@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Invoices</h1>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary">New Invoice</a>
</div>

<table class="table table-striped">
    <thead>
        <tr><th>ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
        @foreach($invoices as $inv)
            <tr>
                <td>{{ $inv->id }}</td>
                <td>{{ optional($inv->customer)->name }}</td>
                <td>{{ $inv->date }}</td>
                <td>{{ $inv->total }}</td>
                <td>{{ $inv->status }}</td>
                <td>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('invoices.show', $inv) }}">View</a>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('invoices.edit', $inv) }}">Edit</a>
                    <form action="{{ route('invoices.destroy', $inv) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $invoices->links() }}
@endsection
