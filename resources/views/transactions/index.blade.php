@extends("layouts.app")

@section("content")
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Transactions</h1>
        <a href="{{ route("transactions.create") }}" class="btn btn-primary">New Transaction</a>
    </div>

    <table class="table-striped table">
        <thead>
            <tr>
                <th>الحساب</th>
                <th>المبلغ</th>
                <th>نوع الحساب</th>
                <th>التاريخ</th>
                {{-- <th>Actions</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $t)
                <tr>
                    <td>{{ optional($t->bank)->name }}</td>
                    <td>{{ $t->amount }}</td>
                    <td> حساب {{__('trans.'.$t->type)  }}   </td>
                    <td>{{ $t->date }}</td>
                    {{-- <td>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route("transactions.show", $t) }}">View</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route("transactions.edit", $t) }}">Edit</a>
                        <form action="{{ route("transactions.destroy", $t) }}" method="POST" style="display:inline">@csrf
                            @method("DELETE")<button class="btn btn-sm btn-danger">Delete</button></form>
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $transactions->links() }}
@endsection
