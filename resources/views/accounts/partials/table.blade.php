<table id="dataTable" class="table-striped table">
    <thead>
        <tr>
            <th>اسم البنك/الحساب</th>
            <th>الرقم</th>
            <th>النوع</th>
            <th>الرصيد</th>
            <th>اجراء</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($accounts as $account)
            <tr>
                <td>{{ $account->name }}</td>
                {{-- <td>{{ $account->customer }}</td> --}}
                <td>{{ __("trans." . $account->number)   }}</td>
                <td>{{ __("trans." . $account->type) }}</td>
                <td>{{ ($account->kind === "payment" ? ' - '.number_format($account->balance ?? 0, 2)   : ' + ' . number_format($account->balance ?? 0, 2)  ) }}</td>
                <td >
                    <a href="{{ route("accounts.show", $account->id) }}"
                        class="btn btn-sm btn-secondary">عرض</a>

                    @if ($account->kind === "receipt")
                        <a class="btn btn-success" href="#" data-bs-toggle="modal"
                            data-bs-target="#receiveModal-{{ $account->id }}"
                            href="{{ route("transactions.receipt.create") }}?account_id={{ $account->id }}">سند
                            قبض</a>
                        <!-- Add Supplier Modal -->
                        <div class="modal fade" id="receiveModal-{{ $account->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">سند قبض</h5>
                                    </div>
                                    <div class="modal-body">

                                        <form class="parsley-style-1"
                                            action="{{ route("transactions.receipt.store") }}" method="POST">
                                            @csrf
                                            <div class=" d-none mb-3">
                                                <label class="form-label"> من حساب العميل ( اختياري)</label>
                                                <select name='customer_id' class="form-control">
                                                    <option value="">عميل افتراضي</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}"
                                                            @if ($account->id == $customer->account_id) selected @endif>
                                                            {{ $customer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">دفع المبلغ كاش</label>
                                                <input type="number" step="0.01" name="amount"
                                                    class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label for="bank" class="form-label">
                                                    دفع المبلغ بنكي</label>

                                                <input type="number" name="bank" class="form-control"
                                                    value="{{ old("Bank") }}">
                                                @error("TransactionType")
                                                    <span class="text-danger" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                @enderror

                                            </div>

                                            <div hidden class="mb-3">
                                                <label class="form-label">التاريخ</label>
                                                <input disabled type="date" name="date"
                                                    class="form-control" value="{{ now()->format("Y-m-d") }}">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">الوصف</label>
                                                <input type="text" name="description" class="form-control">
                                            </div>

                                            <button class="btn btn-primary">تسجيل سند القبض</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($account->kind === "payment")
                        <a class="btn btn-primary" href="#" data-bs-toggle="modal"
                            data-bs-target="#paymentModal-{{ $account->id }}">سند دفع</a>

                        <div class="modal fade" id="paymentModal-{{ $account->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" > سند دفع الى {{$account->name}}</h5>
                                    </div>
                                    <div class="modal-body">

                                        <form class="parsley-style-1"
                                            action="{{ route("transactions.payment.store") }}" method="POST">
                                            @csrf

                                            <div class="mb-3">

                                                @foreach ($suppliers as $supplier)
                                                    @if ($account->id == $supplier->account_id)
                                                        <input type="hidden" name="supplier_id"
                                                            value="{{ $supplier->id }}" class="form-control">
                                                    @endif
                                                @endforeach


                                                <select name="account_id" class="d-none form-control">
                                                      <option value="">

                                                            اختر الحساب
                                                            </option>
                                                    {{-- @isset($Banks)
                                                        @foreach ($Banks as $bank )
                                                            <option value="{{ $bank->id }}">

                                                                {{ $bank->name }}
                                                            </option>
                                                        @endforeach
                                                    @endisset --}}

                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">دفع المبلغ كاش</label>
                                                <input type="number" step="0.01" name="amount"
                                                    class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label for="bank" class="form-label">
                                                    دفع المبلغ بنكي</label>

                                                <input type="number" name="bank" class="form-control"
                                                    >
                                                @error("bank")
                                                    <span class="text-danger" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                @enderror

                                            </div>

                                            <div hidden class="mb-3">
                                                <label class="form-label">التاريخ</label>
                                                <input disabled type="date" name="date"
                                                    class="form-control"
                                                    value="{{ now()->format("Y-m-d") }}">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">الوصف</label>
                                                <input type="text" name="description"
                                                    class="form-control">
                                            </div>

                                            <button class="btn btn-primary">تسجيل سند القبض</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- @if ($account->type === "liability")
                        <a href="{{ route("transactions.payment.create") }}?account_id={{ $account->id }}"
                            class="btn btn-success"> فاتورة </a>
                    @endif --}}
                    <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">حذف</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
