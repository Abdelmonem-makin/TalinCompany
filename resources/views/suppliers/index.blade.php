@extends("layouts.app")

@section("content")
    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>إدارة الموردين</h1>
            <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#addSupplierModal">إضافة مورد</a>
        </div>

        <div class="card p-3">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input class="form-control" id="searchInput" placeholder="بحث بالاسم أو الرقم" oninput="searchTable()">
                </div>

            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table-hover table">
                    <thead>
                        <tr>
                            <th>اسم المورد</th>
                            <th>العنوان</th>
                            <th>الهاتف</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $s)
                            <tr>
                                <td>{{ $s->name }}</td>
                                {{-- <td>{{ $s->email }}</td> --}}
                                <td>{{ $s->address }}</td>
                                <td>{{ $s->phone }}</td>
                                <td>
                                    {{-- <a class="btn btn-sm btn-outline-secondary"
                                        href="{{ route("suppliers.show", $s) }}">View</a> --}}
                                    <a class="btn btn-sm btn-primary" href="#" data-bs-toggle="modal"
                                        data-url="{{ $s->id }}" data-method="get"
                                        data-bs-target="#EditeModal-{{ $s->id }}">تعديل</a>

                                    <div class="modal fade" id="EditeModal-{{ $s->id }}" tabindex="-1" data-bs-backdrop="static"
                                        data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"> تعديل بيانات مورد {{ $s->name }}</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{ route("suppliers.update", $s) }}">
                                                        @csrf @method("PUT")
                                                        <div class="mb-3">
                                                            <label class="form-label">اسم المورد</label>
                                                            <input name="name" class="form-control"
                                                                value="{{ old("name", $s->name) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">البريد الالكتروني</label>
                                                            <input name="email" class="form-control"
                                                                value="{{ old("email", $s->email) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">رقم العاتف</label>
                                                            <input name="phone" class="form-control"
                                                                value="{{ old("phone", $s->phone) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">العنوان</label>
                                                            <textarea name="address" class="form-control">{{ old("address", $s->address) }}</textarea>
                                                        </div>
                                                        <div class="modal-footer">

                                                            <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>

                                                            <button class="btn btn-primary">تحديث</button>
                                                        </div>

                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route("suppliers.destroy", $s) }}" method="POST"
                                        style="display:inline">@csrf @method("DELETE")<button
                                            class="btn btn-sm btn-danger">حذف</button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة مورد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <form id="supplierForm" method="POST" action="{{ route("suppliers.store") }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">اسم المورد</label>
                            <input name="name" class="form-control" value="{{ old("name") }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الريد الالكتروني</label>
                            <input name="email" class="form-control" value="{{ old("email") }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input name="phone" class="form-control" value="{{ old("phone") }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <textarea name="address" class="form-control">{{ old("address") }}</textarea>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>
                            <button id="saveSupplierBtn" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- <table class="table table-striped">
    <thead>
        <tr><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr>
    </thead>
    <tbody>
        @foreach ($suppliers as $s)
            <tr>
                <td>{{ $s->name }}</td>
                <td>{{ $s->email }}</td>
                <td>{{ $s->phone }}</td>
                <td>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('suppliers.show', $s) }}">View</a>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('suppliers.edit', $s) }}">Edit</a>
                    <form action="{{ route('suppliers.destroy', $s) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table> --}}
    <script>
        function searchTable() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let table = document.getElementById('dataTable');
            let tr = table.getElementsByTagName('tr');
            for (let i = 1; i < tr.length; i++) {
                let tds = tr[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < tds.length; j++) {
                    if (tds[j] && tds[j].textContent.toLowerCase().indexOf(input) > -1) {
                        found = true;
                        break;
                    }
                }
                tr[i].style.display = found ? '' : 'none';
            }
        }
    </script>
    {{ $suppliers->links() }}
@endsection
