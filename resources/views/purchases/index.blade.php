@extends("layouts.app")
@section("content")
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>المشتريات</h1>
                 <div class="col-md-4">
                    <input class="form-control" placeholder="بحث بالاسم" oninput="searchTable()" id="searchInput">
                </div>
                <a class="btn btn-sm btn-primary" href="#" data-bs-toggle="modal"
                    data-bs-target="#addFullModal">إضافة مشتريات </a>
            <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#addSupplierModal">إضافة مورد</a>
               

        </div>
    
        <div class="card table-responsive">
            <table id="dataTable" class="table-striped table">
                <thead>
                    <tr>
                        <th>المرجع</th>
                        <th>اسم المورد</th>
                        <th>اجمالي المشتريات</th>
                        <th>اجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchases as $purchase)
                        <tr>
                            <td><a
                                    href="{{ route("purchases.show", $purchase->id) }}">{{ $purchase->reference_id ?? $purchase->id }}</a>
                            </td>
                            <td> {{ optional($purchase->supplier)->name }}</td>
                            <td>{{ number_format($purchase->total, 2) }}</td>
                            <td>

                                <button class="btn btn-sm btn-warning editpurchasesFullBtn" data-id="{{ $purchase->id }}"
                                    data-reference="{{ $purchase->reference_id ?? $purchase->id }}"
                                    data-supplier="{{ $purchase->supplier_id }}" data-bs-toggle="modal"
                                    data-bs-target="#editpurchasesFullBtn">
                                    تعديل
                                </button>

                                <button class="btn btn-sm btn-success printBtn" data-id="{{ $purchase->id }}"
                                    data-reference="{{ $purchase->reference_id ?? $purchase->id }}"
                                    data-supplier="{{ optional($purchase->supplier)->name }}"
                                    data-date="{{ $purchase->date ? $purchase->date->format("Y-m-d") : now()->format("Y-m-d") }}"
                                    data-total="{{ number_format($purchase->total, 2) }}" data-bs-toggle="modal"
                                    data-bs-target="#printModal">
                                    طباعة
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

        <!-- Modal for Add Full Purchase -->
        <div class="modal fade" id="addFullModal" tabindex="-1" aria-labelledby="addFullModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFullModalLabel">إضافة مشتريات </h5>
                    </div>
                    <div class="modal-body">
                        <form id="addFullForm" method="POST" action="{{ route("purchases.store-full") }}"
                            class="parsley-style-1">
                            @csrf
                            <div class="row">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="modal-search-input"
                                            placeholder="البحث عن المنتجات..." oninput="searchModalProducts()">
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="table-responsive">
                                        <table id="dataTable2" class="table-bordered table-striped table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>اسم المنتج</th>
                                                    <th>الكمية المتاحة</th>
                                                    {{-- <th>سعر البيع</th> --}}
                                                    <th>الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody id="add-products-table">
                                                <!-- Products will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <h3>طلبيات المشتريات</h3>
                                    <div class="cart-purchase-shoping row">
                                        <div class="order-list" id="add-full-order-list">
                                            <!-- Cart items will be loaded here -->
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mt-3">
                                        <label for="add-supplier-id"
                                            class="col-md-4 col-form-label text-md-start">المورد</label>
                                        <div class="col-md-8">
                                            <select class="form-select" name="supplier_id" id="add-supplier-id" required>
                                                <option value="">اختر مورد</option>
                                                @foreach ($suppliers as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <script>
                                        function searchModalProducts() {
                                            let input = document.getElementById('modal-search-input').value.toLowerCase();
                                            let table = document.getElementById('dataTable2');
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
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <h4>الإجمالي: <span id="add-total-price">0.00</span> </h4>
                                        </div>
                                        <div class="col-md-12 aling-self-center">
                                            <button type="submit" class="btn btn-success btn-block">إضافة
                                                المشتريات</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Edit Full Purchase -->
        <div class="modal fade" id="editpurchasesFullBtn" tabindex="-1" aria-labelledby="editFullModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFullModalLabel">تعديل المشتريات</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editFullForm" method="POST" class="parsley-style-1">
                            @csrf
                            @method("PUT")
                            <input type="hidden" name="purchase_id" id="edit-purchase-id">

                            <div class="row">
                                <div class="col-md-7">
                                    <div class="table-responsive">
                                        <table class="table-bordered table-striped table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>اسم المنتج</th>
                                                    <th>الكمية المتاحة</th>
                                                    <th>الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody id="edit-products-table">
                                                <!-- Products will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <h3>طلبيات المشتريات</h3>
                                    <div class="cart-purchase-shoping row">
                                        <div class="order-list" id="edit-order-list">
                                            <!-- Cart items will be loaded here -->
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mt-3">
                                        <label for="edit-supplier-id"
                                            class="col-md-4 col-form-label text-md-start">المورد</label>
                                        <div class="col-md-8">
                                            <select class="form-select" name="supplier_id" id="edit-supplier-id"
                                                required>
                                                <option value="">اختر مورد</option>
                                                @foreach ($suppliers as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <h4>الإجمالي: <span id="edit-total-price">0.00</span> </h4>
                                        </div>
                                        <div class="col-md-12 aling-self-center">
                                            <button type="submit" class="btn btn-success btn-block">تحديث
                                                المشتريات</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Invoice Print -->
        <div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="printModalLabel">طباعة الفاتورة</h5>
                    </div>
                    <div class="modal-body">
                        <div id="invoice-print-content">
                            <div class="mb-4 text-center">
                                <h2>فاتورة مشتريات</h2>
                                <p><strong>رقم الفاتورة:</strong> <span id="modal-reference-id"></span></p>
                                <p><strong>تاريخ:</strong> <span id="modal-date"></span></p>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>معلومات المورد</h5>
                                    <p><strong>الاسم:</strong> <span id="modal-supplier"></span></p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <h5>معلومات الشركة</h5>
                                    <p><strong>تالين</strong></p>
                                </div>
                            </div>
                            <table class="table-bordered table">
                                <thead>
                                    <tr>
                                        <th>اسم الصنف</th>
                                        <th>الكمية</th>
                                        <th>سعر الوحدة</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-lines">
                                    <!-- Lines will be populated via JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">الإجمالي الكلي:</th>
                                        <th id="modal-total"></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="mt-4 text-center">
                                <p>شكراً لتعاملكم معنا</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="button" class="btn btn-primary" id="print-modal-btn">طباعة</button>
                    </div>
                </div>
            </div>
        </div>
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
                        {{-- <div class="mb-3">
                            <label class="form-label">الريد الالكتروني</label>
                            <input name="email" class="form-control" value="{{ old("email") }}">
                        </div> --}}
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input name="phone" class="form-control" value="{{ old("phone") }}">
                        </div>
                        {{-- <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <textarea name="address" class="form-control">{{ old("address") }}</textarea>
                        </div> --}}
                        <div class="modal-footer">
                            <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>
                            <button id="saveSupplierBtn" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
        {{ $purchases->links() }}
    @endsection
