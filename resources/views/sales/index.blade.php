@extends("layouts.app")
@section("content")
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>المبيعات</h1>
     <div class="col-md-4">
                    <input class="form-control" placeholder="بحث بالاسم" oninput="searchTable()" id="searchInput">
                </div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalId">
                مبيعات جديدة !
            </button>

            <!-- Modal -->
            <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
                aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitleId">
                                مبيعات جديدة !

                            </h5>
                        </div>
                        <div class="modal-body">
                            <div class="card-header">
                                <div class="d-flex justify-content-start">

                                    <div class="col-md-4">
                                        <input type="text" class="form-control my-2" id="modal-search-input"
                                            placeholder="البحث عن المنتجات..." oninput="searchModalProducts()">
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
                                </div>
                            </div>
                            <div class="card-body mt-auto">

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                @if ($items->count() > 0)
                                                    <table id="dataTable2"
                                                        class="table-bordered table-striped mg-b-0 text-md-nowrap table p-0 text-center">
                                                        <thead>
                                                            <tr>
                                                                <th> #</th>
                                                                <th> اسم الدواء</th>
                                                                <th>الكميه</th>
                                                                <th>السعر </th>
                                                                <th>الاجراءات</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @isset($items)
                                                                @foreach ($items as $index => $Product)
                                                                    <tr>
                                                                        <td class="pt-4" scope="row">
                                                                            {{ $index + 1 }}</td>
                                                                        <td scope="row">{{ $Product->name }}</td>
                                                                        <td scope="row">{{ $Product->stock }}</td>
                                                                        <td scope="row">{{ $Product->price }}</td>

                                                                        <td>
                                                                            <a id="product-sales{{ $Product->id }}"
                                                                                data-name="{{ $Product->name }}"
                                                                                data-id="{{ $Product->id }}"
                                                                                data-price="{{ $Product->price }}"
                                                                                class="btn btn-dark add-product_sales-btn my-0 px-4 py-0"
                                                                                href="">
                                                                                <i class="fa fa-plus"
                                                                                    aria-hidden="true"></i>اضافه
                                                                            </a>

                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endisset
                                                        </tbody>
                                                    </table>
                                                    {{ $items->links() }}
                                                @else
                                                    <h4 class="text-center">لا توجد سجلات للعرض</h4>
                                                @endif
                                            </div><!-- bd -->
                                        </div><!-- bd -->
                                    </div>
                                    <div class="col-md-4">
                                        <div class="">
                                            <!-- Cart Items -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <h3>طلبيات الصيدليه </h3>
                                                    <form method="POST" action="{{ route("sales.store") }}"
                                                        class="parsley-style-1">
                                                        @csrf
                                                        <div class="cart-sales-shoping row">
                                                            <div class="order-list">

                                                            </div>
                                                        </div>

                                                        <!-- Cart Summary -->
                                                        <div class="col-12">
                                                            <div class="cart-summary">

                                                                <div class="row">
                                                                    <label for="customer_id"
                                                                        class="col-md-4 col-form-label text-md-start">
                                                                        العميل</label>
                                                                    <div class="col-md-8">
                                                                        <div class="mb-3">
                                                                            <select
                                                                                class="form-select form-select-md @error("customer_id") is-invalid @enderror"
                                                                                name="customer_id" id="customer_id"
                                                                                data-placeholder=" اختار عميل ....."
                                                                                style="width:100%">
                                                                                <option value="" selected>عميل
                                                                                    افتراضي</option>

                                                                                @isset($customers)
                                                                                    @foreach ($customers as $id => $name)
                                                                                        <option value="{{ $id }}">
                                                                                            {{ $name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                @endisset
                                                                            </select>
                                                                            @error("TransactionType")
                                                                                <span class="text-danger" role="alert">
                                                                                    {{ $message }}
                                                                                </span>
                                                                            @enderror
                                                                        </div>

                                                                    </div>
                                                                </div>

                                                                <hr>

                                                                <div class="row mb-4">
                                                                    <div class="col-md-12">
                                                                        <h4>الإجمالي: <div class="total-price">0.00</div>
                                                                        </h4>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <button type="submit" id="add-sales-btn"
                                                                            class="btn w-100 btn-success disabled my-3 text-center">
                                                                            تاكيد الطلب
                                                                        </button>
                                                                        {{-- div> --}}
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <script>
                                    (function() {
                                        const form = document.getElementById('salesForm');
                                        const submitBtn = document.getElementById('add-sales-btn');

                                        form.addEventListener('submit', async function(e) {
                                            e.preventDefault();
                                            submitBtn.disabled = false;

                                            const formData = new FormData(form);

                                            try {
                                                const res = await fetch('{{ route("sales.store") }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: formData
                                                });

                                                const data = await res.json();

                                                if (data.success) {
                                                    // Clear cart items
                                                    var id = $(this).data('id');

                                                    document.querySelectorAll('.cart-sales-shoping').forEach(el => el.innerHTML = '');
                                                    // Reset form fields (payment, transiction_no, etc.)
                                                    form.reset();
                                                    // Disable submit since cart is empty (visual + property)
                                                    submitBtn.classList.add('disabled');
                                                    submitBtn.disabled = true; // keep for direct DOM compatibility
                                                    $(submitBtn).prop('disabled', true);
                                                    // Optionally re-enable product buttons if you disabled them earlier
                                                    $('.add-sales-btn').removeClass('btn-default disabled').addClass(
                                                        'btn-dark');
                                                    // ensure the global calculat() logic sees the button as disabled
                                                    $('#add-sales-btn').prop('disabled', true);

                                                    // Show success feedback
                                                    alert(data.message || 'تم اضافة الفاتور بنجاج  بنجاح');
                                                } else {
                                                    alert(data.message || 'حدث خطأ أثناء إنشاء الطلب');
                                                    // Keep the submit button enabled so user can retry
                                                    submitBtn.disabled = false;
                                                }
                                            } catch (err) {
                                                alert(data.error);
                                                submitBtn.disabled = false;
                                            }
                                        });
                                    })();
                                </script>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                الغاء
                            </button>
                        </div>
                    </div>
                </div>
            </div>

         

        </div>
    
        <div class="card p-3">
            <table id="dataTable" class="table-striped table">
                <thead>
                    <tr>
                        <th>رفم الفاتورة</th>
                        <th>اسم العميل</th>
                        <th>احمالي المبلغ</th>
                        <th>التاريخ</th>
                        <th>الحاله</th>
                        <th>اجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>{{ optional($sale->customer)->name ? optional($sale->customer)->name : "عميل افتراضي" }}
                            </td>
                            <td>{{ number_format($sale->total, 2) }}</td>
                            <td>{{ $sale->date->format("Y-m-d") }}</td>
                            <td>{{ $sale->status }}</td>
                            <td>
                                <button class="Show-product btn btn-sm btn-outline-primary my-1" data-bs-toggle="modal"
                                    data-url="{{ route("show-sales-order", $sale->id) }}" data-bs-target="#show_modalId"
                                    data-method="get">عرض الطلبات</button>

                                <div class="modal fade" id="show_modalId" tabindex="-1" data-bs-backdrop="static"
                                    data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalTitleId">
                                                    قائمة الطلبات
                                                </h5>

                                            </div>
                                            <div class="modal-body">
                                                <div class="col-md-12">
                                                    <div class="list-order-product">

                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    رجوع
                                                </button>
                                                <a href="http://" class="btn print-order-btn btn-success">
                                                    طباعه</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ $sale->customer_id }}" class="btn btn-sm btn-warning editSalesFullBtn"
                                    data-id="{{ $sale->id }}" data-reference="{{ $sale->id }}"
                                    data-customer="{{ $sale->customer_id }}" data-bs-toggle="modal"
                                    data-bs-target="#editSalesFullBtn">
                                    تعديل
                                </a>
                                <form action="{{ route("sales.destroy", $sale) }}" method="POST"
                                    style="display:inline-block">
                                    @csrf
                                    @method("DELETE")
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete sale?')">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal for Add Full Purchase -->

        <div class="modal fade" id="editSalesFullBtn" tabindex="-1" aria-labelledby="editFullModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFullModalLabel">تعديل المبيعات</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editFullForm" method="POST" class="parsley-style-1">
                            @csrf
                            @method("PUT")
                            <input type="hidden" name="purchase_id" id="edit-sales-id">

                            <div class="row">
                                <div class="col-md-7">
                                    <div class="table-responsive">
                                        <table class="table-bordered table-striped table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>اسم المنتج</th>
                                                    <th>الكمية المتاحة</th>
                                                    <th>السعر</th>
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
                                    <h3>طلبيات المبيعات</h3>
                                    <div class="cart-purchase-shoping row">
                                        <div class="order-list" id="edit-order-list">
                                            <!-- Cart items will be loaded here -->
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="mb-3">
                                        <label for='edit-supplier-id'class="form-label">اختار اسم العميل </label>
                                        <select name="customer_id" class="form-control" id='edit-customer-id'>
                                            <option value=""> عميل افتراضي</option>

                                            @isset($customers)
                                                @foreach ($customers as $id => $name)
                                                    <option value="{{ $id }}">
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            @endisset
                                        </select>
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
        {{ $sales->links() }}
    </div>

    bs5-ta
@endsection
