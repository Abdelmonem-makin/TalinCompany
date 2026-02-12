@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">إدارة الصلاحيات</h1>
        <a href="{{ route('permissions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة صلاحية جديدة
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة الصلاحيات</h5>
                <div class="input-group" style="width: 300px;">
                    <input type="text" id="searchInput" class="form-control" placeholder="البحث في الصلاحيات..."
                           onkeyup="searchTable()">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($permissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="dataTable">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>اسم الصلاحية</th>
                                <th>الاسم المعروض</th>
                                <th>الوصف</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $permission->name }}</strong>
                                    </td>
                                    <td>{{ $permission->display_name }}</td>
                                    <td>
                                        <span title="{{ $permission->description }}">
                                            {{ Str::limit($permission->description, 50) }}
                                        </span>
                                    </td>
                                    <td>{{ $permission->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('permissions.edit', $permission) }}"
                                               class="btn btn-sm btn-warning"
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $permissions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-key fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد صلاحيات</h5>
                    <p class="text-muted">ابدأ بإضافة صلاحية جديدة لتتمكن من إدارة صلاحيات المستخدمين</p>
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة الصلاحية الأولى
                    </a>
                </div>
            @endif
        </div>
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
@endsection
