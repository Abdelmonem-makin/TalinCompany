@extends('layouts.app')

@section('content')
<h1>Invoice Lines</h1>
<a class="btn btn-primary mb-3" href="{{ route('invoice-lines.create') }}">New Line</a>
<table class="table">
    <thead><tr><th>Invoice</th><th>Item</th><th>Qty</th><th>Unit Price</th><th>Total</th><th>Actions</th></tr></thead>
    <tbody>
        @foreach($lines as $line)
            <tr>
                <td>{{ $line->invoice_id }}</td>
                <td>{{ optional($line->item)->name }}</td>
                <td>{{ $line->quantity }}</td>
                <td>{{ $line->unit_price }}</td>
                <td>{{ $line->total }}</td>
                <td>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('invoice-lines.show', $line) }}">View</a>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('invoice-lines.edit', $line) }}">Edit</a>
                    <form action="{{ route('invoice-lines.destroy', $line) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $lines->links() }}
@endsection
