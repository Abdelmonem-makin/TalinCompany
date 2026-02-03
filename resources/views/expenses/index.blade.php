@extends("layouts.app")

@section("content")
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>المنصرفات</h1>
        <div class="col-md-4">
            <input class="form-control" placeholder="بحث بالاسم" oninput="searchTable()" id="searchInput">
        </div>
        <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#addExpensesModal"> اضافة مصروفات </a>
    </div>

    <table class="table-striped table">
        <thead>
            <tr>
                <th> المصروف</th>
                <th>المبلغ</th>
                <th>التاريخ</th>
                <th>الوصف</th>
                <th>اجراء</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $exp)
                <tr>
                    <td>{{ optional($exp->account)->name }}</td>
                    <td>{{ $exp->amount }}</td>
                    <td>{{ $exp->date }}</td>
                    <td>{{ $exp->category }}</td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route("expenses.edit", $exp) }}">تعديل</a>
                        <form action="{{ route("expenses.destroy", $exp) }}" method="POST" style="display:inline">@csrf
                            @method("DELETE")<button class="btn btn-sm btn-danger">حذف</button></form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="modal fade" id="addExpensesModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">اضافة مصروفات</h5>
                </div>
                <div class="modal-body">

                    <form method="POST" action="{{ route("expenses.store") }}">
                        @csrf

                        <div class="mb-3">
                            <label for="category" class="form-label"> تصنيف المنصرف</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">اختر التصنيف</option>
                                <option value="إيجار">إيجار</option>
                                <option value="خدمات">خدمات</option>
                                <option value="صيانة">صيانة</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">حساب الدفع</label>
                            <select name="account_id" class="form-select">
                                <option value="">--</option>
                                @foreach ($accounts as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">المبلغ </label>
                            <input name="amount" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="address" class="form-control"> </textarea>
                        </div>
                        <div class="modal-footer">

                            <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>
                            <button class="btn btn-primary">اضافه</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

    {{ $expenses->links() }}
@endsection
