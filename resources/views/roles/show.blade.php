@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تفاصيل الدور: {{ $role->display_name }}</h5>
                    <div>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">معلومات أساسية</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">اسم الدور:</td>
                                    <td>{{ $role->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">الاسم المعروض:</td>
                                    <td>{{ $role->display_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">الوصف:</td>
                                    <td>{{ $role->description ?: 'لا يوجد وصف' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">تاريخ الإنشاء:</td>
                                    <td>{{ $role->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">آخر تحديث:</td>
                                    <td>{{ $role->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">إحصائيات</h6>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-primary">{{ $role->permissions->count() }}</h4>
                                            <small class="text-muted">صلاحية</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-success">{{ $role->users->count() }}</h4>
                                            <small class="text-muted">مستخدم</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">الصلاحيات الممنوحة</h6>
                            @if($role->permissions->count() > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($role->permissions as $permission)
                                        <span class="badge bg-primary">{{ $permission->display_name ?: $permission->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">لا توجد صلاحيات ممنوحة لهذا الدور</p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">المستخدمون المرتبطون</h6>
                            @if($role->users->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($role->users->take(5) as $user)
                                        <div class="list-group-item px-0 py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ $user->name }}</span>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($role->users->count() > 5)
                                        <div class="list-group-item px-0 py-2 text-center">
                                            <small class="text-muted">و {{ $role->users->count() - 5 }} مستخدم آخر...</small>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted">لا يوجد مستخدمون مرتبطون بهذا الدور</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
