@extends("layouts.app")

@section("content")
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>الموظفين</h1>
        <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#addSupplierModal">إضافة موظف</a>

    </div>

    <table class="table-striped table">
        <thead>
            <tr>
                <th>اسم الموظف</th>
                <th>القسم</th>
                <th>القسم</th>
                <th>المرتب</th>
                <th>اجراء</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $e)
                <tr>
                    <td>{{ $e->name }}</td>
                    <td>{{ $e->phone }}</td>
                    <td>{{ $e->position }}</td>
                    <td>{{ $e->salary }}</td>
                    <td>
                        <a class="btn btn-sm btn-secondary" href="{{ route("employees.show", $e) }}">عرض</a>

                        <a class="btn btn-sm btn-primary" href="{{ route("employees.edit", $e) }}">تعديل</a>
                        <a class="btn btn-sm btn-info" href="#" data-bs-toggle="modal"
                            data-bs-target="#addsalaryModal">صرف مرتب </a>

                        <form action="{{ route("employees.destroy", $e) }}" method="POST" style="display:inline">@csrf
                            @method("DELETE")<button class="btn btn-sm btn-danger">حذف</button></form>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $employees->links() }}

    <!-- Add custamers Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة موظف</h5>
                </div>
                <div class="modal-body">

                    <form method="POST" action="{{ route("employees.store") }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">اسم الموظف</label>
                            <input name="name" class="form-control" value="{{ old("name") }}">
                        </div>
                        {{-- <div class="mb-3">
                            <label class="form-label">البريد الالكتروني</label>
                            <input name="email" class="form-control" value="{{ old("email") }}">
                        </div> --}}
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input name="phone" class="form-control" value="{{ old("phone") }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">القسم</label>
                            <input name="position" class="form-control" value="{{ old("position") }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">المرتب الاساسي</label>
                            <input name="salary" class="form-control" value="{{ old("salary", 0) }}">
                        </div>
                        <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>
                        <button class="btn btn-primary">اضافه</button>
                    </form>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

    <!-- Add salary Modal -->
    <div class="modal fade" id="addsalaryModal" tabindex="-2">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة مرتب</h5>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route("payroll-transactions.store") }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">اسم الموظف</label>
                            <select name="employee_id" class="form-select">
                                <option value="">----</option>
                                @foreach ($employees as   $name)
                                    <option value="{{ $name->id }}" @if (request("account_id") == $name->id) selected @endif>{{ $name->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input name="amount" class="form-control" value="{{ old("amount") }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ old("date") }}">
                        </div>
                        <button class="btn btn-primary">Create</button>
                    </form>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
@endsection
