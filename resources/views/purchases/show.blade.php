@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>   مشتريات المورد / {{ $purchase->supplier->name }} </h1>
        <div>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">رجوع</a>
            {{-- <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning">Edit</a> --}}
        </div>
    </div>


    <div class="card mb-3">
        <div class="card-body">
            <p><strong>اسم المورد:</strong> {{ optional($purchase->supplier)->name }}</p>
            {{-- <p><strong>Date:</strong> {{ $purchase->date->format('Y-m-d') }}</p> --}}
            <p><strong>الحاله :</strong> {{ $purchase->status }}</p>
            <p><strong>اجمالي المشتريات:</strong> {{ number_format($purchase->total, 2) }}</p>
        </div>
    </div>

    <h4>الشتريات</h4>
    <table class="table table-bordered mb-3">
        <thead>
            <tr>
                <th>اسم الصنف</th>
                <th>الكميه</th>
                <th>  سعر الوحده</th>
                <th>الاجمالي</th>
                <th>اجراس</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->purchaseLines as $line)
            <tr>
                <td>{{ optional($line->item)->name }}</td>
                <td>{{ $line->quantity }}</td>
                <td>{{ number_format($line->unit_price, 2) }}</td>
                <td>{{ number_format($line->total, 2) }}</td>
                <td>
                    <form action="{{ route('purchase-lines.destroy', $line) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete line?')">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <h4>مشتريات جديده!</h4>
    <form action="{{ route('purchase-lines.store') }}" method="POST" class="mb-4">
        @csrf
        <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">

        <div class="row">
            <div class="col-md-5">
                <label for="item_id" class="form-label">المنتج</label>
                <select name="item_id" id="item_id" class="form-control" required>
                    <option value="">  اختار منتج</option>
                    @foreach($items as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="quantity" class="form-label">الكميه</label>
                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" required>
            </div>

            <div class="col-md-2">
                <label for="unit_price" class="form-label">سعر الوحده  </label>
                <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" required>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-success">شراء</button>
            </div>
        </div>
    </form>

</div>
@endsection
