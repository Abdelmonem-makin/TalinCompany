@extends('layouts.app')

@section('content')
<div class="container">
    <h1>تعديل بيانات مورد مشتريات</h1>


    <form action="{{ route('purchases.update', $purchase) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="supplier_id" class="form-label">المورد</label>
            <select name="supplier_id" id="supplier_id" class="form-control" required>
                <option value="">  اختار مورد</option>
                @foreach($suppliers as $id => $name)
                    <option value="{{ $id }}" {{ $purchase->supplier_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $purchase->date->format('Y-m-d')) }}" required>
        </div> --}}

        {{-- <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="draft" {{ $purchase->status == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="received" {{ $purchase->status == 'received' ? 'selected' : '' }}>Received</option>
                <option value="cancelled" {{ $purchase->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div> --}}

        <button type="submit" class="btn btn-primary">تحديث بياتات مورد المشتريات</button>
    </form>
</div>
@endsection
