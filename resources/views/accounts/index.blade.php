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
                            <td>{{ $account->number }}</td>
                            <td>{{ __("trans." . $account->type) }}</td>
                            <td>{{ number_format($account->balance ?? 0, 2) }}</td>
                            <td>
                                <a href="{{ route("accounts.show", $account->id) }}"
                                    class="btn btn-sm btn-outline-secondary">عرض</a>

                                @if ($account->kind === "receipt")
                                    <a class="btn btn-success" href="#" data-bs-toggle="modal"
                                        data-bs-target="#receiveModal"
                                        href="{{ route("transactions.receipt.create") }}?account_id={{ $account->id }}">سند
                                        قبض</a>
                                    <!-- Add Supplier Modal -->
                                    <div class="modal fade" id="receiveModal" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">سند قبض</h5>
                                                  
                                                </div>
                                                <div class="modal-body">

                                           
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @elseif($account->kind === "payment")
                                    <a href="{{ route("transactions.payment.create") }}?account_id={{ $account->id }}"
                                        class="btn btn-danger">سند دفع</a>
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
