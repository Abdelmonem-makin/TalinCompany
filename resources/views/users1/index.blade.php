@extends("layouts.app")

@section("content")
    <main class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>المستخدمون</h1>
                 <div class="col-md-4">
                    <input class="form-control" placeholder="بحث بالاسم أو البريد الإلكتروني" oninput="searchTable()" id="searchInput">
                </div>
            <a class="btn btn-primary" href="{{ route('users.create') }}">إضافة مستخدم</a>

        </div>

            <table id="dataTable" class="table-striped table">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الأدوار</th>
                        <th>الصلاحيات المباشرة</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach ($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($user->permissions as $permission)
                                    <span class="badge bg-secondary">{{ $permission->name }}</span>
                                @endforeach
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-secondary"
                                    href="{{ route("users.show", $user) }}">عرض</a>
                                <a class="btn btn-sm btn-primary" href="{{ route("users.edit", $user) }}">تعديل</a>
                                <form action="{{ route("users.destroy", $user) }}" method="POST"
                                    style="display:inline">@csrf @method("DELETE")
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    </main>

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
    {{ $users->links() }}
@endsection
