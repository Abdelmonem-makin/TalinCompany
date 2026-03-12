@extends('layouts.app')

@section('content')
<div class="container">
    <h1> اضافة مورد مشتريات </h1>


    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="supplier_id" class="form-label">اسم المورد</label>
            <select name="supplier_id" id="supplier_id" class="form-control" required>
                <option value="">  اختار امورد مشتريات </option>
                @foreach($suppliers as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
        </div> --}}

        {{-- <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="draft">Draft</option>
                <option value="received">Received</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div> --}}

        <button type="submit" class="btn btn-primary">اضافه  </button>
    </form>

    <hr>
</div>
@endsection
