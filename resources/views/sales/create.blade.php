@extends("layouts.app")

@section("content")
    {{-- <div class="container">
    <h1>Create Sale</h1>

    @include('partials.alerts')

    <form action="{{ route('sales.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="customer_id" class="form-label">Customer</label>
            <select name="customer_id" id="customer_id" class="form-control">
                <option value="">Select customer</option>
                @foreach ($customers as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="draft">Draft</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Create Sale</button>
    </form>

    <hr>
    <p class="text-muted">After creating the sale, add sale lines (items) on the sale details page.</p>
</div> --}}

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-start">
                {{-- <a href="{{ route("User.index") }}" class="nav nav-link me-a">المخزون</a> --}}
                <h3 class="me-a">-</h3>
                <p class="nav text-dark nav-link me-a"> اضافة منتج الى المخزون </p>
            </div>
        </div>
        <div class="card-body mt-auto">
            {{-- @if (Session::has("success"))
                <div class="alert alert-success" role="alert">
                    <p class="text-center">{{ Session::get("success") }}</p>
                </div>
            @endif
            @if (Session::has("error"))
                <div id="alertBox" class="alert alert-danger" role="alert">
                    <p class="text-center">{{ Session::get("error") }}</p>
                </div>
            @endif --}}
            <div class="row">
                <div class="col-md-7">
                    <div class="card-body">
                        <div class="table-responsive">
                            @if ($items->count() > 0)
                                <table class="table-bordered table-striped mg-b-0 text-md-nowrap table p-0 text-center">
                                    <thead>
                                        <tr>
                                            <th> #</th>
                                            <th> اسم الدواء</th>
                                            {{-- <th>الصورة</th> --}}
                                            <th>الكميه</th>
                                            {{-- <th>الوصف</th> --}}
                                            {{-- <th>سعر الشراء</th> --}}
                                            <th>السعر </th>
                                            {{-- <th>الحاله</th>     --}}
                                            <th>الاجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($items)
                                            @foreach ($items as $index => $Product)
                                                <tr>
                                                    <th class="pt-4" scope="row">{{ $index + 1 }}</th>
                                                    <th scope="row">{{ $Product->name }}</th>
                                                    <th scope="row">{{ $Product->stock }}</th>
                                                    <th scope="row">{{ $Product->price }}</th>

                                                    <th>
                                                        <a id="product-sales{{ $Product->id }}"
                                                            data-name="{{ $Product->name }}" data-id="{{ $Product->id }}"
                                                            data-price="{{ $Product->price }}"
                                                            class="btn btn-dark add-product_sales-btn my-0 px-4 py-0"
                                                            href="">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>اضافه
                                                        </a>

                                                    </th>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                </table>
                            @else
                                <h4 class="text-center">لا توجد سجلات للعرض</h4>
                            @endif
                        </div><!-- bd -->
                        {{-- {!! $Products->appends(request()->search)->links() !!} --}}
                    </div><!-- bd -->
                </div>
                <div class="col-md-5">
                    <div class="">
                        <!-- Cart Items -->
                        <div class="row">
                            <div class="col-12">
                                <h3>طلبيات الصيدليه </h3>
                                <form method="POST" action="{{ route("sales.store") }}" class="parsley-style-1">
                                    {{ csrf_field() }}
                                    {{-- {{ method_field('post') }} --}}
                                    <div class="cart-sales-shoping row">
                                        <div class="order-list">

                                        </div>
                                    </div>

                                    <!-- Cart Summary -->
                                    <div class="col-12">
                                        <div class="cart-summary">


                                            <div class="row">
                                                <label for="customer_id" class="col-md-4 col-form-label text-md-start">
                                                    العميل</label>
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <select
                                                            class="form-select form-select-md @error("customer_id") is-invalid @enderror"
                                                            name="customer_id" id="customer_id"
                                                            data-placeholder=" اختار عميل ....." style="width:100%">
                                                            <option value="" selected>عميل افتراضي</option>

                                                            @isset($customers)
                                                                @foreach ($customers as $customer)
                                                                    <option value="{{ $customer->id }}">
                                                                        {{ $customer->name }}
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
@endsection
