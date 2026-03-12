@extends("layouts.app")

@section("content")
    <main class="container py-4">
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">إدارة المخزون</h1>
            <div>
                <a class="btn btn-success" href="#" data-bs-toggle="modal" data-bs-target="#receiveModal">استلام من
                    مورد</a>
                <a class="btn btn-outline-primary ms-2" href="#" data-bs-toggle="modal" data-bs-target="#issueModal">صرف
                    للعميل</a>
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
                            <th>النوع</th>
                            <th>المنتج / مرجع</th>
                            <th>SKU / Batch</th>
                            <th>الكمية</th>
                            <th>ملاحظات</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stocks as $s)
                            <tr>
                                <td>{{ $s->created_at->format("Y-m-d H:i") }}</td>
                                <td>{{ ucfirst($s->type ?? "manual") }}</td>
                                <td>{{ optional($s->item)->name }} @if ($s->reference_id)
                                        / {{ $s->reference_id }}
                                    @endif
                                </td>
                                <td>{{ optional($s->item)->sku ?? "-" }}</td>
                                <td>{{ $s->change }}</td>
                                <td>{{ $s->note }}</td>
                                <td>
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
                            <div class="mb-2">
                                <label class="form-label">المورد</label>
                                <select name="supplier_id" class="form-control">
                                    <option value="">اختر المورد</option>
                                    @foreach ($suppliers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">المنتج</label>
                                <select name="item_id" class="form-control" required>
                                    <option value="">اختر المنتج</option>
                                    @foreach ($items as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">الكمية</label>
                                    <input class="form-control" type="number" name="change" value="0" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم التشغيلة</label>
                                    <input class="form-control" name="reference_id">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">تاريخ الانتهاء</label>
                                    <input class="form-control" type="date" name="expiry">
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>
                        <button id="saveReceiveBtn" class="btn btn-success">حفظ</button>
                    </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Issue Modal -->
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
                            <div class="mb-2">
                                <label class="form-label">العميل</label>
                                <select name="customer_id" class="form-control">
                                    <option value="">اختر العميل</option>
                                    @foreach (\App\Models\Customer::pluck("name", "id") as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">المنتج</label>
                                <select name="item_id" class="form-control" required>
                                    <option value="">اختر المنتج</option>
                                    @foreach ($items as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">الكمية</label>
                                    <input class="form-control" type="number" name="change" value="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم التشغيلة</label>
                                    <input class="form-control" name="reference_id">
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

        <!-- Expired Modal -->
        <div class="modal fade" id="expiredModal" tabindex="-1">
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
        </div>

    </main>

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
