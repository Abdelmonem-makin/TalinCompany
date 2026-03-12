@extends('layouts.app')

@section('content')
<div class="container">
    <h1>سند دفع (Payment)</h1>


    <form action="{{ route('transactions.payment.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">حساب السداد (البنك/الصندوق)</label>
            <select name="account_id" class="form-control"  >
                <option value="">اختر الحساب</option>
                @foreach($accounts as $id => $name)
                    <option value="{{ $id }}" @if(request('account_id') == $id) selected @endif>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">المورد (اختياري)</label>
            <select name="supplier_id" class="form-control">
                <option value="">---</option>
                @foreach($suppliers as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">المبلغ</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">التاريخ</label>
            <input type="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">الوصف</label>
            <input type="text" name="description" class="form-control">
        </div>

        <button class="btn btn-primary">تسجيل سند الدفع</button>
    </form>
</div>
@endsection
