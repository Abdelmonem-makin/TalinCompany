@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">إدارة الأدوار والصلاحيات</h1>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة دور جديد
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة الأدوار</h5>
                <div class="input-group" style="width: 300px;">
                    <input type="text" id="searchInput" class="form-control" placeholder="البحث في الأدوار..."
                           onkeyup="searchTable()">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($roles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="dataTable">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>اسم الدور</th>
                                <th>الاسم المعروض</th>
                                <th>الوصف</th>
                                <th>عدد الصلاحيات</th>
                                <th>عدد المستخدمين</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $role->name }}</strong>
                                    </td>
                                    <td>{{ $role->display_name }}</td>
                                    <td>
                                        <span title="{{ $role->description }}">
                                            {{ Str::limit($role->description, 50) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $role->permissions->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $role->users->count() }}</span>
                                    </td>
                                    <td>{{ $role->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('roles.show', $role) }}"
                                               class="btn btn-sm btn-info"
                                               title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('roles.edit', $role) }}"
                                               class="btn btn-sm btn-warning"
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($role->users->count() == 0)
                                                <form method="POST" action="{{ route('roles.destroy', $role) }}"
                                                      style="display: inline;"
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-secondary"
                                                        disabled
                                                        title="لا يمكن حذف دور مرتبط بمستخدمين">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $roles->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد أدوار</h5>
                    <p class="text-muted">ابدأ بإضافة دور جديد لتتمكن من إدارة صلاحيات المستخدمين</p>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة الدور الأول
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
