@extends("layouts.app")
@section("content")
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>المبيعات</h1>
            <a href="{{ route("sales.create") }}" class="btn btn-primary">انشاء مبيعات جديدة !</a>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <input class="form-control" value='-' id="searchInput" placeholder="بحث بالاسم أو الرقم" oninput="searchTable()">
            </div>
        </div>
        <div class="card p-3">
            <table id="dataTable" class="table-striped table">
                <thead>
                    <tr>
                        <th>الرقم</th>
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
                            <td>c-{{ $sale->id }}</td>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>-{{ optional($sale->customer)->name ? optional($sale->customer)->name :"عميل افتراضي"}}</td>
                            <td>{{ number_format($sale->total, 2) }}</td>
                            <td>{{ $sale->date->format("Y-m-d") }}</td>
                            <td>{{ $sale->status }}</td>
                            <td>
                                <button class="Show-product btn btn-sm btn-outline-primary my-1" data-bs-toggle="modal"
                                    data-url="{{ route("show-sales-order", $sale->id) }}" data-bs-target="#modalId"
                                    data-method="get">عرض الطلبات</button>

                                <div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static"
                                    data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm"
                                        role="document">
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
                                {{-- <a href="{{ route("sales.show", $sale) }}" class="btn btn-sm btn-info">View</a> --}}
                                {{-- <a href="{{ route("sales.edit", $sale) }}" class="btn btn-sm btn-warning">تعديل</a> --}}
                                <a class="btn btn-sm btn-primary" href="#" data-bs-toggle="modal"
                                    data-url="{{ $sale->id }}" data-method="get"
                                    data-bs-target="#EditeModal-{{ $sale->id }}">تعديل</a>
                                <div class="modal fade" id="EditeModal-{{ $sale->id }}" tabindex="-1"
                                    data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
                                    aria-labelledby="modalTitleId" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">تعديل بيانات عميل  </h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route("sales.update", $sale->id) }}"  method="PUT">
                                                    @csrf
                                                    {{-- @method("PUT") --}}
                                                    <div class="mb-3">
                                                        <label class="form-label">اختار اسم العميل </label>
                                                        <select name="supplier_id" class="form-control">
                                                            <option  value=""> عميل افتراضي</option>
                                                       @foreach ($customers as $id => $name )
                                                                <option value="{{ $id }}" 
                                                                   @if ( $id == $sale->customer_id) selected @endif >
                                                                    {{ $name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
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
        {{ $sales->links() }}
    </div>
@endsection
