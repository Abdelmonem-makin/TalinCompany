@extends("layouts.app")
@section("content")
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>المشتريات</h1>
            <a class="btn btn-sm btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#EditeModal">اضافة مورد
                مشتريات </a>
            <div class="modal fade" id="EditeModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
                role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">اضافة بيانات مورد مشتريات</h5>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route("purchases.store") }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">اختار اسم المورد </label>
                                    <select name="supplier_id" id="supplier_id" class="form-control" required>
                                        <option value=""> اختار مورد</option>
                                        @foreach ($suppliers as $id => $name)
                                            <option value="{{ $id }}">
                                                {{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>
                                    <button class="btn btn-primary">اضافة</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <input value='' class="form-control" id="searchInput" placeholder="بحث بالاسم أو الرقم"
                    oninput="searchTable()">
            </div>
        </div>
        <div class="card table-responsive">
            <table id="dataTable" class="table-striped table">
                <thead>
                    <tr>
                        <th>رقم </th>
                        <th>اسم المورد</th>
                        <th>اجمالي المشتريات</th>
                        <th>اجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->id }}</td>
                            <td> {{ optional($purchase->supplier)->name }}</td>
                            <td>{{ number_format($purchase->total, 2) }}</td>
                            <td>
                                <a href="{{ route("purchases.show", $purchase) }}" class="btn btn-sm btn-info">عرض </a>

                                <button class="btn btn-sm btn-primary editBtn" data-id="{{ $purchase->id }}"
                                    data-supplier="{{ $purchase->supplier_id }}" data-total="{{ $purchase->total }}"
                                    data-bs-toggle="modal" data-bs-target="#editModal">
                                    تعديل
                                </button>

                                <form action="{{ route("purchases.destroy", $purchase) }}" method="POST"
                                    style="display:inline-block">
                                    @csrf
                                    @method("DELETE")
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete purchase?')">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editForm" method="POST">
                        @csrf
                        @method("PUT")
                        <div class="modal-header">
                            <h5 class="modal-title">تعديل بيانات مورد مشتريات</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="purchase_id" id="purchase_id">

                            <div class="mb-3">
                                <label class="form-label">اختار اسم المورد</label>
                                <select name="supplier_id" id="supplier_id" class="form-control">
                                    @foreach ($suppliers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">اجمالي المشتريات</label>
                                <input type="text" id="total" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button class="btn btn-primary">تحديث</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.dataset.id;
                    let supplierId = this.dataset.supplier;
                    let total = this.dataset.total;

                    let modal = document.querySelector('#editModal');
                    let supplierSelect = modal.querySelector('#supplier_id');

                    // تعبئة الحقول
                    modal.querySelector('#purchase_id').value = id;
                    modal.querySelector('#total').value = total;

                    // تحديد المورد الصحيح
                    supplierSelect.value = supplierId;

                    // لو ما اتطابق، حدد يدويًا
                    if (supplierSelect.value !== supplierId) {
                        [...supplierSelect.options].forEach(opt => {
                            if (opt.value == supplierId) {
                                opt.selected = true;
                            }
                        });
                    }

                    // تحديث الفورم بالمسار الصحيح
                    modal.querySelector('#editForm').action = "/purchases/" + id;
                });
            });
        </script>
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

        {{ $purchases->links() }}
    @endsection
