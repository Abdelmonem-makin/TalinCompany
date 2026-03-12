@extends("layouts.app")
@section("content")
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>المبيعات</h1>
            <a href="{{ route("sales.create") }}" class="btn btn-primary">انشاء مبيعات جديدة !</a>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <input class="form-control" placeholder="بحث بالاسم أو الرقم">
            </div>
        </div>
        {{-- @include('partials.alerts') --}}
        <div class="card p-3">
            <table class="table-striped table">
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
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>{{ optional($sale->customer)->name }}</td>
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
                                <a href="{{ route("sales.edit", $sale) }}" class="btn btn-sm btn-warning">تعديل</a>

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
