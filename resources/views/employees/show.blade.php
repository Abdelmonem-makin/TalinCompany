@extends("layouts.app")

@section("content")
    <h1>الموظف : {{ $employee->name }}</h1>
    <ul class="list-unstyled">
        {{-- <li><strong>Email:</strong> {{ $employee->email }}</li> --}}
        <li><strong>رقم الهاتف:</strong> {{ $employee->phone }}</li>
        <li><strong>القسم:</strong> {{ $employee->position }}</li>
        <li><strong>المرتب:</strong> {{ $employee->salary }}</li>
    </ul>
    <a href="{{ route("employees.index") }}" class="btn btn-secondary">رجوع</a>
    <table class="table">
        <thead>
            <tr>
                <th>الحساب</th>
                <th>التاريخ</th>
                <th>اجراء</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employee->payrollTransactions as $r)
                <tr>
                    {{-- <td>{{ optional($r->employee)->name }}</td> --}}
                    <td>{{ $r->amount }}</td>
                    <td>{{ $r->date }}</td>
                    <td>
                        {{-- <a class="btn btn-sm btn-outline-secondary" href="{{ route('payroll-transactions.show', $r) }}">عرض</a> --}}
                        <a class="btn btn-sm btn-outline-primary"
                            href="{{ route("payroll-transactions.edit", $r) }}">تعديل</a>
                        <form action="{{ route("payroll-transactions.destroy", $r) }}" method="POST" style="display:inline">
                            @csrf @method("DELETE")<button class="btn btn-sm btn-danger">حذف</button></form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- {{ $rows->links() }} --}}
@endsection
