@extends("layouts.app")

@section("content")
    <main class="container   px-0 py-4">
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4"> حركات المخزون </h1>
            <div class="col-md-4">
                <input class="form-control" id="searchInput" type="text" placeholder="بحث بالاسم أو الرقم"
                    oninput="searchTable()">
            </div>
                    @if ($expiringSoonStocks->count() > 0)
                     <a class="btn btn-outline-info ms-2" href="#" data-bs-toggle="modal" data-bs-target="#expiringSoonModal"
               >  منتجات تنتهي خلال 7 أيام{{ $expiringSoonStocks->count() }}</a>
            {{-- <div class="card col-6 border-info mb-3 p-3"> 
                <h6 class="text-info">تنبيه: منتجات تنتهي خلال 7 أيام</h6>
                <p class="mb-2">يوجد {{ $expiringSoonStocks->count() }} منتج سينتهي خلال الأسبوع القادم. يرجى مراجعة
                    المخزون واتخاذ الإجراءات المناسبة.</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                        data-bs-target="#expiringSoonModal">عرض المنتجات القريبة من الانتهاء</button>
                </div>
            </div> --}}
        @endif
                  @if ($expiredStocks->count() > 0)
                   <a class="btn btn-outline-danger ms-2" href="#" data-bs-toggle="modal" data-bs-target="#expiredModal"
                id="expiredModalBtn">     ( {{ $expiredStocks->count() }} )  من المنتجات المنتهية </a>
            {{-- <div class="card col-6 border-danger mb-3 p-3">
                <h6 class="text-danger">تحذير: منتجات منتهية الصلاحية</h6>
                <p class="mb-2">يوجد {{ $expiredStocks->count() }} منتج منتهي الصلاحية. يرجى مراجعة المخزون والتخلص من
                    المنتجات المنتهية فوراً.</p>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#expiredModal">عرض
                    المنتجات المنتهية</button>
            </div> --}}
        @endif 
        </div>
        <div class="row">
 


        </div>
        <div class="card mt-3 p-3">
            <div class="table-responsive">
                <table id="dataTable" id="movementsTable" class="table-sm table">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>نوع العميله</th>
                            <th>المنتج</th>
                            <th>صرف/استلام</th>
                            <th>الكمية</th>
                            <th>الفاتوره</th>
                            <th>الحاله</th>
                            <th>انتهاء الصلاحيه</th>
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
                                <td>{{ $s->remaining }}</td>
                                <td>{{ $s->quantity }}</td>
                                <td>{{ $s->type == "مبيعات" ? $s->Sales->invoice_number ?? "-" : $s->purchase->reference_id ?? "-" }}
                                </td>
                                <td>{{__("trans." .  $s->status) }}</td>
                                <td>{{ $s->expiry ? $s->expiry->format("Y-m-d") : "غير محدد"  }}</td>
                                <td>{{ $s->note }}</td>
                                <td>
                                    @if ($s->type == "مبيعات" && $s->status == "draft")
                                        <a class="btn issuebtn btn-sm btn-primary" href="#"
                                            data-reference_id='{{ $s->reference_id }}' data-id='{{ $s->id }}'
                                            data-customer_id='{{ optional($s->Sales)->customer_id }}'
                                            data-item_id='{{ $s->item->id }}' data-bs-toggle="modal"
                                            data-bs-target="#issueModal">تاكيد صرف </a>
                                    @endif

                                    @if ($s->type == "مشتريات" && $s->status == "draft")
                                        @php
                                            $purchase = \App\Models\Purchases::find($s->reference_id);
                                        @endphp
                                        @if (!$purchase || $purchase->status !== "received")
                                            <a class="btn receivebtn btn-sm btn-success"
                                                data-reference_id='{{ $s->reference_id }}' data-id='{{ $s->id }}'
                                                data-expiry='{{ $s->expiry }}' data-change='{{ $s->quantity }}'
                                                data-supplier='{{ optional($s->purchase)->supplier_id }}'
                                                data-item_id='{{ $s->item->id }}' data-bs-toggle="modal"
                                                data-bs-target="#receiveModal">تاكيد استلام
                                            </a>
                                        @endif
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
                            <input name='stock' id='stockid' type="hidden">
                            <input name='reference_id' id='reference_id' type="hidden"
                                value="{{ $s->reference_id ?? "" }}">
                            <div class="mb-2">
                                <label class="form-label">العميل</label>
                                <select name="customer_id" id='customer_id' class="form-control">
                                    <option value=""> عميل افتراضي</option>
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

                                {{-- <div class="col-md-4">
                                    <label class="form-label">ملاحظات</label>
                                    <input class="form-control" name="note">
                                </div> --}}
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
                            <input name='reference_id' id='reference_id' type="hidden"
                                value="{{ $s->reference_id ?? "" }}">
                            <input name='stock' id='stockid' type="hidden">
                            <div class="mb-2">
                                <label class="form-label">المورد</label>
                                <select name="supplier_id" id='supplier_id' class="form-control">
                                    <option value=""> مورد افتراضي</option>
                                    @foreach ($suppliers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">المنتج</label>
                                <select name="item_id" id='item_id' class="form-control" required>
                                    <option value="">اختر المنتج</option>
                                    @foreach ($items as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">الكمية</label>
                                    <input class="form-control" type="number" id='change'name="change"
                                        value="0" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">تاريخ الانتهاء</label>
                                    <input class="form-control" type="date" id='expiry' name="expiry" required>
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

        <!-- Expiring Soon Modal -->
        <div class="modal fade col-6" id="expiringSoonModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">المنتجات القريبة من الانتهاء</h5><button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted">القائمة التالية تعرض المنتجات التي ستنتهي صلاحيتها خلال الأسبوع القادم.
                            يُنصح بمراجعة هذه المنتجات واتخاذ الإجراءات المناسبة.</p>
                        <div class="table-responsive">
                            <table class="table-sm table" id="expiringSoonTable">
                                <thead>
                                    <tr>
                                        <th>المنتج</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th>الأيام المتبقية</th>
                                        <th>الكمية المتاحة</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expiringSoonStocks as $stock)
                                        <tr>
                                            <td>{{ $stock->item->name ?? "غير محدد" }}</td>
                                            <td>{{ $stock->expiry ? $stock->expiry->format("Y-m-d") : "غير محدد" }}</td>
                                            <td>
                                                @if ($stock->expiry)
                                                    <span
                                                        class="badge bg-{{ now()->diffInDays($stock->expiry) <= 3 ? "danger" : "warning" }}">
                                                        {{ now()->diffInDays($stock->expiry) }} يوم
                                                    </span>
                                                @else
                                                    غير محدد
                                                @endif
                                            </td>
                                            <td>{{ $stock->quantity }}</td>
                                            <td>
                                                @if ($stock->expiry)
                                                    @if (now()->diffInDays($stock->expiry) <= 3)
                                                        <span class="text-danger">عاجل جداً</span>
                                                    @elseif(now()->diffInDays($stock->expiry) <= 7)
                                                        <span class="text-warning">قريب</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <a href="{{ route("stock.index") }}" class="btn btn-primary">إدارة المخزون</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expired Modal -->
        <div class="modal fade col-6" id="expiredModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">إدارة الأصناف المنتهية</h5><button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <form id="disposeForm" action="{{ route('stock.dispose') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p class="small text-muted">القائمة التالية تعرض الباتشات التي انتهت صلاحيتها حتى اليوم. يمكنك
                                إدخال كمية للتخلص منها (ستُخصم من المخزون وتُسجَّل كعملية التخلص).</p>
                            <div class="table-responsive">
                                <table class="table-sm table" id="expiredBatchesTable">
                                    <thead>
                                        <tr>
                                            <th>المنتج</th>
                                            <th>تاريخ الانتهاء</th>
                                            <th>الكمية المتاحة</th>
                                            <th>كمية التخلص</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($expiredStocks as $stock)
                                            <tr>
                                                <td>{{ $stock->item->name ?? "غير محدد" }}</td>
                                                <td>{{ $stock->expiry ? $stock->expiry->format("Y-m-d") : "غير محدد" }}</td>
                                                <td>{{ $stock->quantity }}</td>
                                                <td>
                                                    <input type="number" name="disposals[{{ $stock->id }}]" class="form-control form-control-sm dispose-qty"
                                                        data-stock-id="{{ $stock->id }}" min="0"
                                                        max="{{ $stock->quantity }}" value="0">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-danger">تسجيل التخلص</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
    <script>
        document.querySelectorAll('.issuebtn').forEach(btn => {
            btn.addEventListener('click', function() {
                let stockid = this.dataset.id;
                let reference_id = this.dataset.reference_id;
                let customer_id = this.dataset.customer_id;
                let item_id = this.dataset.item_id;

                let modal = document.querySelector('#issueModal');

                // تعبئة الحقول
                modal.querySelector('#stockid').value = stockid;
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
                modal.querySelector('#stockid').value = id;
                modal.querySelector('#item_id').value = item_id;
                modal.querySelector('#expiry').value = expiry;
                modal.querySelector('#change').value = change;
                modal.querySelector('#supplier_id').value = supplier;
                modal.querySelector('#reference_id').value = reference_id;


                // تحديث الفورم بالمسار الصحيح
                modal.querySelector('#receiveForm').action = "/admin/stock"; // استخدم الـ action الأصلي
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
