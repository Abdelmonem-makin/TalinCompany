@extends("layouts.app")

@section("content")
    <main class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>العملاء</h1>
                 <div class="col-md-4">
                    <input class="form-control" placeholder="بحث بالاسم" oninput="searchTable()" id="searchInput">
                </div>
            <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#addSupplierModal">إضافة عميل</a>

        </div>
 
            <table id="dataTable" class="table-striped table">
                <thead>
                    <tr>
                        <th>اسم المورد</th>
                        <th> العنوان</th>
                        <th>رقم العاتف</th>
                        <th>اجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $cust)
                        <tr>
                            <td>{{ $cust->name }}</td>
                            <td>{{ $cust->address }}</td>
                            <td>{{ $cust->phone }}</td>
                            <td>
                                {{-- <a class="btn btn-sm btn-outline-secondary"
                                    href="{{ route("customers.show", $cust) }}">عرض</a> --}}
                                <a class="btn btn-sm btn-primary" href="#" data-bs-toggle="modal"
                                    data-bs-target="#EditeModal-{{ $cust->id }}">تعديل</a>

                                <div class="modal fade" id="EditeModal-{{ $cust->id }}" tabindex="-1"
                                    data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
                                    aria-labelledby="modalTitleId" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">تعديل بيانات عميل</h5>
                                            </div>
                                            <div class="modal-body">

                                                <form method="POST" action="{{ route("customers.update", $cust->id) }}">
                                                    @csrf @method("PUT")
                                                    <div class="mb-3">
                                                        <label class="form-label">اسم العميل</label>
                                                        <input name="name" class="form-control"
                                                            value="{{ $cust->name }}">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">رقم الهاتف</label>
                                                        <input name="phone" class="form-control"
                                                            value="{{ $cust->phone }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">العنوان</label>
                                                        <textarea name="address" class="form-control">{{ $cust->address }}</textarea>
                                                    </div>
                                                    <div class="modal-footer">

                                                        <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>
                                                        <button class="btn btn-primary">تحديث</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route("customers.destroy", $cust) }}" method="POST"
                                    style="display:inline">@csrf @method("DELETE")<button
                                        class="btn btn-sm btn-danger">حذف</button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    </main>
    <div class="modal fade" id="addSupplierModal" tabindex="-1" data-bs-backdrop="static"
        data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">اضافة بيانات عميل</h5>
                </div>
                <div class="modal-body">

                    <form method="POST" action="{{ route("customers.store") }}">
                        @csrf  
                        <div class="mb-3">
                            <label class="form-label">اسم العميل</label>
                            <input name="name" class="form-control" >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input name="phone" class="form-control" >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <textarea name="address" class="form-control"> </textarea>
                        </div>
                        <div class="modal-footer">

                            <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>
                            <button class="btn btn-primary">تحديث</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

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
    {{ $customers->links() }}
@endsection
