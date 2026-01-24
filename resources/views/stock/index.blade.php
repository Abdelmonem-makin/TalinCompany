@extends("layouts.app")

@section("content")
    <main class="container py-4">
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">إدارة المخزون</h1>
            <div>
                {{-- <a class="btn btn-success" href="#" data-bs-toggle="modal" data-bs-target="#receiveModal">استلام من
                    مورد</a> --}}

                <a class="btn btn-outline-danger ms-2" href="#" data-bs-toggle="modal" data-bs-target="#expiredModal"
                    id="manageExpiredBtn">إدارة المنتهية</a>
            </div>
        </div>
        {{-- <div class="card p-3">
            <h6>تنبيهات قرب الانتهاء</h6>
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">دواء أ - SKU123 <span class="badge badge-expiry">6 أيام</span></li>
            </ul>
        </div> --}}
        <div class="card mt-3 p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">حركات المخزون</h6>
                <div>
                    <select id="movementsFilter" class="form-select form-select-sm d-inline-block" style="width:180px;">
                        <option value="all">الكل</option>
                        <option value="receive">استلام</option>
                        <option value="issue">صرف</option>
                        <option value="dispose">تخلص</option>
                    </select>
                    <button class="btn btn-sm btn-outline-primary ms-2" data-export-table="#movementsTable">تصدير
                        CSV</button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="movementsTable" class="table-sm table">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>نوع العميله</th>
                            {{-- <th> اسم العميل / المورد  </th> --}}
                            <th>المنتج</th>
                            <th> Batch</th>
                            <th>الكمية</th>
                            {{-- <th>صرف / استلام</th> --}}
                            <th>الفاتوره</th>
                            <th>ملاحظات</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stocks as $s)
                            <tr>
                                <td>{{ $s->created_at->format("Y-m-d H:i") }}</td>
                                <td>{{ ucfirst($s->type ?? "manual") }}</td>
                                {{-- <td>{{ $s->purchase }}</td> --}}
                                <td>{{ optional($s->item)->name }}</td>
                                <td>{{ optional($s->item)->sku ?? "-" }}</td>
                                <td>{{ $s->change }}</td>
                                <td>{{ $s->type == "مبيعات" ? $s->Sales->invoice_number ?? "-" : $s->purchase->id ?? "-" }}
                                </td>
                                <td>{{ $s->note }}</td>
                                <td>
                                    @if ($s->type == "مبيعات")
                                        <a class="btn issuebtn btn-sm btn-primary" href="#"
                                            data-reference_id='{{ $s->reference_id }}' data-id='{{ $s->id }}'
                                            data-customer_id='{{ $s->Sales->customer_id }}'
                                            data-item_id='{{ $s->item->id }}' data-bs-toggle="modal"
                                            data-bs-target="#issueModal">صرف
                                            الى عميل</a>
                                    @endif

                                    @if ($s->type == "مشتريات")
                                        <a class="btn  receivebtn btn-sm btn-success" 
                                          data-reference_id='{{ $s->reference_id }}' 
                                          data-id='{{ $s->id }}'
                                          data-expiry='{{ $s->expiry }}'
                                          data-change='{{ $s->change }}'
                                            data-supplier='{{ $s->purchase->supplier_id?? "" }}'
                                            data-item_id='{{ $s->item->id }}' data-bs-toggle="modal"
                                            data-bs-target="#receiveModal"
                                            >استلام من
                                            مورد</a>
                                    @endif

                                    {{-- <a href="{{ route('stock.show', $s) }}" class="btn btn-sm btn-info">عرض</a> --}}
                                    {{-- <a href="{{ route('stock.edit', $s) }}" class="btn btn-sm btn-warning">تعديل</a> --}}
                                    <form action="{{ route("stock.destroy", $s) }}" method="POST"
                                        style="display:inline-block">
                                        @csrf
                                        @method("DELETE")
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('حذف الحركة؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $stocks->links() }}
            </div>
        </div>
        <div class="modal fade" id="issueModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">صرف كميات لعميل</h5><button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="issueForm" action="{{ route("stock.store") }}" method="POST">
                            @csrf
                            <input name='reference_id' id='reference_id' type="hidden" value="{{ $s->reference_id }}">
                            <div class="mb-2">
                                <label class="form-label">العميل</label>
                                <select name="customer_id" id='customer_id' class="form-control">
                                    <option value="">اختر العميل</option>
                                    @foreach (\App\Models\Customer::pluck("name", "id") as $id => $name)
                                        <option value="{{ $id }}">
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">المنتج</label>
                                <select name="item_id" id='item_id' class="form-control" required>
                                    <option value="">اختر المنتج</option>
                                    @foreach ($items as $id => $name)
                                        <option value="{{ $id }}">
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">الكمية</label>
                                    <input class="form-control" type="number" name="change" value="1" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">ملاحظات</label>
                                    <input class="form-control" name="note">
                                </div>
                            </div>
                            <input type="hidden" name="type" value="issue">
                            <input type="hidden" name="sign" value="-1">

                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button id="saveIssueBtn" class="btn btn-primary">تأكيد
                            الصرف</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Receive Modal -->
        <div class="modal fade" id="receiveModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">استلام كميات من مورد</h5><button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="receiveForm" action="{{ route("stock.store") }}" method="POST">
                            @csrf
                            <input name='reference_id' id='reference_id' type="hidden" value="{{ $s->reference_id }}">
                            <div class="mb-2">
                                <label class="form-label">المورد</label>
                                <select   name="supplier_id" id='supplier_id' class="form-control">
                                    <option value="">اختر المورد</option>
                                    @foreach ($suppliers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">المنتج</label>
                                <select    name="item_id" id='item_id' class="form-control" required>
                                    <option value="">اختر المنتج</option>
                                    @foreach ($items as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">الكمية</label>
                                    <input class="form-control" type="number" id='change'name="change" value="0" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">تاريخ الانتهاء</label>
                                    <input class="form-control" type="date" id='expiry' name="expiry"  >
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>
                        <button id="saveReceiveBtn" class="btn btn-success">تاكيد استلام</button>
                    </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Issue Modal -->

        <!-- Expired Modal -->
        {{-- <div class="modal fade" id="expiredModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">إدارة الأصناف المنتهية</h5><button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted">القائمة التالية تعرض الباتشات التي انتهت صلاحيتها حتى اليوم. يمكنك
                            إدخال كمية للتخلص منها (ستُخصم من المخزون وتُسجَّل كعملية التخلص).</p>
                        <div class="table-responsive">
                            <table class="table-sm table" id="expiredBatchesTable">
                                <thead>
                                    <tr>
                                        <th>المنتج</th>
                                        <th>SKU</th>
                                        <th>Batch</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th>الموجود</th>
                                        <th>كمية التخلص</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button id="disposeBatchesBtn" class="btn btn-danger">تسجيل التخلص</button>
                    </div>
                </div>
            </div>
        </div> --}}

    </main>
    <script>
        document.querySelectorAll('.issuebtn').forEach(btn => {
            btn.addEventListener('click', function() {
                let id = this.dataset.id;
                let reference_id = this.dataset.reference_id;
                let customer_id = this.dataset.customer_id;
                let item_id = this.dataset.item_id;

                let modal = document.querySelector('#issueModal');

                // تعبئة الحقول
                modal.querySelector('#item_id').value = item_id;
                modal.querySelector('#customer_id').value = customer_id;
                modal.querySelector('#reference_id').value = reference_id;

                // لو ما اتطابق، حدد يدويًا
                if (customerSelecter.value !== customer_id) {
                    [...customerSelecter.options].forEach(opt => {
                        if (opt.value == customer_id) {
                            opt.selected = true;
                        }
                    });
                }

                // تحديث الفورم بالمسار الصحيح
                // modal.querySelector('#issueForm').action = "/stock/" + id;
            });
        });

          document.querySelectorAll('.receivebtn').forEach(btn => {
            btn.addEventListener('click', function() {
                let id = this.dataset.id;
                let expiry = this.dataset.expiry;
                let change = this.dataset.change;
                let reference_id = this.dataset.reference_id;
                let supplier = this.dataset.supplier;
                let item_id = this.dataset.item_id;

                let modal = document.querySelector('#receiveModal');

                // تعبئة الحقول
                modal.querySelector('#item_id').value = item_id;
                modal.querySelector('#expiry').value = expiry;
                modal.querySelector('#change').value = change;
                modal.querySelector('#supplier_id').value = supplier;
                modal.querySelector('#reference_id').value = reference_id;

        
                // تحديث الفورم بالمسار الصحيح
                modal.querySelector('#receiveForm').action = "/admin/stock" ; // استخدم الـ action الأصلي
            });
        });
    </script>
    @push("scripts")
        <script>
            // submit receive form to server
            document.getElementById('saveReceiveBtn').addEventListener('click', function() {
                const form = document.getElementById('receiveForm');
                if (!form.reportValidity()) return;
                form.submit();
            });
            // submit issue form to server
            document.getElementById('saveIssueBtn').addEventListener('click', function() {
                const form = document.getElementById('issueForm');
                if (!form.reportValidity()) return;
                form.submit();
            });
        </script>
    @endpush
@endsection
