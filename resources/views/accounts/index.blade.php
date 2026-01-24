@extends("layouts.app")
@section("content")
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>الحسابات</h1>
            <a href="{{ route("accounts.create") }}" class="btn btn-primary">اضافة حساب جديد</a>
            {{-- <a href="{{ route("transactions.index") }}" class="btn btn-primary">المعاملات</a> --}}
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <input class="form-control" id="searchInput" placeholder="بحث بالاسم أو الرقم" oninput="searchTable()">
            </div>
        </div>
        <!-- resources/views/accounts/index.blade.php -->
        <div class="card p-3">
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
                            <td>{{ $account->number }}</td>
                            <td>{{ __("trans." . $account->type) }}</td>
                            <td>{{ number_format($account->balance ?? 0, 2) }}</td>
                            <td>
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
                                                        <div class="mb-3">
                                                            <label class="form-label"> من حساب العميل ( اختياري)</label>
                                                            <select disabled name="customer_id" class="form-control">
                                                                <option value="">عميل افتراضي</option>
                                                                @foreach ($customers as $customer)
                                                                    <option value="{{ $customer->id }}"
                                                                        @if ($account->id == $customer->account_id) selected @endif>
                                                                        {{ $customer->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        {{-- <div class="mb-3">
                                                            <label class="form-label"> الى حساب الاستلام
                                                                (البنك/الصندوق)
                                                            </label>

                                                            <select name="account_id" class="form-control">
                                                                <option value="">اختر الحساب</option>
                                                                @foreach ($account as $id => $name)
                                                                    <option value="{{ $id }}"
                                                                        @if (request("account_id") == $id) selected @endif>
                                                                        {{ $name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div> --}}
                                                        <div class="mb-3">
                                                            <label class="form-label">دفع المبلغ كاش</label>
                                                            <input type="number" step="0.01" name="amount"
                                                                class="form-control">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="bank" class="form-label">
                                                                دفع المبلغ بنكي</label>
                                                             
                                                                    <input type="number" name="bank"
                                                                        class="form-control" value="{{ old("Bank") }}">
                                                                    @error("TransactionType")
                                                                        <span class="text-danger" role="alert">
                                                                            {{ $message }}
                                                                        </span>
                                                                    @enderror
                                                             
                                                        </div>

                                                        <div hidden class="mb-3">
                                                            <label  class="form-label">التاريخ</label>
                                                            <input disabled   type="date" name="date" class="form-control"
                                                                value="{{ now()->format("Y-m-d") }}">
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
                                    <a href="{{ route("transactions.payment.create") }}?account_id={{ $account->id }}"
                                        class="btn btn-danger">سند دفع</a>
                                @endif

                                @if ($account->type === "liability")
                                    <a href="{{ route("transactions.payment.create") }}?account_id={{ $account->id }}"
                                        class="btn btn-success"> فاتورة </a>
                                @endif
                                {{-- <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-sm btn-warning">تعديل</a> --}}
                                {{-- <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('حذف الحساب؟')">حذف</button>
                                </form> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function searchTable() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let table = document.getElementById('dataTable');
            let tr = table.getElementsByTagName('tr');
            for (let i = 1; i < tr.length; i++) {
                let tds = tr[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < tds.length; j++) {
                    if (tds[j] && tds[j].textContent.toLowerCase().indexOf(input) > -1) {
                        found = true;
                        break;
                    }
                }
                tr[i].style.display = found ? '' : 'none';
            }
        }
    </script>
    {{ $accounts->links() }}
@endsection
