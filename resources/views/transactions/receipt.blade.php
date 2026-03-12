@extends("layouts.app")

@section("content")
    <div class="container">
        <h1>سند قبض (Receipt)</h1>

        <form class="parsley-style-1" action="{{ route("transactions.receipt.store") }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label"> من حساب العميل (  اختياري)</label>
                <select name="customer_id" class="form-control">
                    <option value="">---</option>
                    @foreach ($customers as $id => $name)
                        <option value="{{ $id }}"  @if (request("account_id") == $id) selected @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"> الى حساب الاستلام (البنك/الصندوق)</label>
                <select name="account_id" class="form-control">
                    <option value="">اختر الحساب</option>
                    @foreach ($accounts as $id => $name)
                        <option value="{{ $id }}" @if (request("account_id") == $id) selected @endif>
                            {{ $name }}</option>
                    @endforeach
                </select>
            </div>
                        <div class="mb-3">
                <label class="form-label">المبلغ</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="customer_id" class="form-label">
                    دفع بنكي</label>
                <div class="col-md-8">
                    <div class="mb-3">
                        <input type="number" name="bank" class="form-control" value="{{ old("Bank") }}">
                        @error("TransactionType")
                            <span class="text-danger" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>




            <div class="mb-3">
                <label class="form-label">التاريخ</label>
                <input type="date" name="date" class="form-control" value="{{ now()->format("Y-m-d") }}">
            </div>

            <div class="mb-3">
                <label class="form-label">الوصف</label>
                <input type="text" name="description" class="form-control">
            </div>

            <button class="btn btn-primary">تسجيل سند القبض</button>
        </form>
    </div>
@endsection
