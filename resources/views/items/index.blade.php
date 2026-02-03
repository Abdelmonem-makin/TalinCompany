@extends("layouts.app")
@section("content")
    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center m-2">
            <h1>المنتجات</h1>
                 <div class="col-md-4">
                    <input class="form-control" placeholder="بحث بالاسم" oninput="searchTable()" id="searchInput">
                </div>
            <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#addSupplierModal"> إضافة منتج
            </a>
        </div>
 
        <div class="card m-0 p-3">
            <table id="dataTable" class="table-striped table">
                <thead>
                    <tr>
                        <th>اسم المنتجات</th>
                        <th>الشركه المصنعه</th>
                        <th>رقم الباتش </th>
                        <th>سعر البيع</th>
                        <th>المخزون</th>
                        <th>اجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $it)
                        <tr>
                            <td>{{ $it->name }}</td>
                            <td>{{ $it->company }}</td>
                            <td>{{ $it->sku }}</td>
                            <td>{{ $it->price }}</td>
                            <td>{{ $it->stock }}</td>
                            {{-- <td>{{ optional($it->supplier)->name }}</td> --}}
                            <td>
                                {{-- <a class="btn btn-sm btn-outline-secondary"
                                        href="{{ route("items.show", $it) }}">View</a> --}}
                                <a class="btn btn-sm btn-primary" href="#" data-bs-toggle="modal"
                                    data-bs-target="#EditeModal">تعديل</a>

                                <div class="modal fade" id="EditeModal" tabindex="-1" data-bs-backdrop="static"
                                    data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">تعديل بيانات منتج</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="{{ route("items.update", $it) }}">
                                                    @csrf @method("PUT")
                                                    <div class="mb-3">
                                                        <label class="form-label">اسم المنتج</label>
                                                        <input name="name" class="form-control"
                                                            value="{{ old("name", $it->name) }}">
                                                    </div>
                                                    {{-- <div class="mb-3">
                                                            <label class="form-label"> كود SKU </label>
                                                            <input name="sku" class="form-control"
                                                                value="{{ old("sku", $item->sku) }}">
                                                        </div> --}}
                                                    <div class="mb-3">
                                                        <label class="form-label">سعر البيع</label>
                                                        <input name="price" class="form-control"
                                                            value="{{ old("price", $it->price) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">المخزون</label>
                                                        <input name="stock" class="form-control"
                                                            value="{{ old("stock", $it->stock) }}">
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
                                <form action="{{ route("items.destroy", $it) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method("DELETE")<button class="btn btn-sm btn-danger">حذف</button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    </main>
    <!-- Add item Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة مورد</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> --}}
                </div>
                <div class="modal-body">
                    <form id="supplierForm" method="POST" action="{{ route("items.store") }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">اسم المنتج</label>
                            <input name="name" class="form-control" value="{{ old("name") }}">
                        </div>
                        {{-- <div class="mb-3">
                            <label class="form-label">الباتش</label>
                            <input name="sku" class="form-control" value="{{ old("sku") }}">
                        </div> --}}
                        <div class="mb-3">
                            <label class="form-label">سعر البيع</label>
                            <input name="price" class="form-control" value="{{ old("price") }}">
                        </div>
                        {{-- <div class="mb-3">
                            <label class="form-label">الشركه المصنعه</label>
                            <input name="company" class="form-control" value="{{ old("company") }}">
                        </div> --}}
                        {{-- <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">--</option>
                                @foreach ($suppliers as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="modal-footer">
                            <a class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</a>
                            <button class="btn btn-primary">اضافه</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
    {{ $items->links() }}
@endsection
