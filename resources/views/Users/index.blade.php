@extends("layouts.app")
@section("content")
    <div class="row row-sm">
        <!--/div-->
        <!--div-->
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header py-0">

                    <div class="d-flex justify-content-between">
                        <h3 class="me-a my-2"> المشرفين <small>{{ $users->total() }}</small></h3>
                        <form class="row g-3 h-25 needs-validation mt-1" action="{{ route("users.index") }}" method="get">
                            <div class="col-md-6 m-0">
                                <input type="text" class="form-control" value="{{ request()->search }}"
                                    id="validationCustom01" name="search">

                            </div>
                            <div class="col-md-6 m-0">
                                <button class="btn btn-primary px-1" type="submit"><i class="fa fa-search mx-1"
                                        aria-hidden="true"></i>بحث</button>
                                @if (auth()->user()->hasPermission("users_create"))
                                    <button type="button" class="btn btn-primary ms-a my-0" data-bs-toggle="modal"
                                        data-bs-target="#createUserModal">
                                        اضافة مشرف
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary disabled ms-a my-0" disabled>
                                        اضافة مشرف
                                    </button>
                                @endif

                            </div>
                        </form>
                        <ol class="breadcrumb my-2">
                            <li><a class="text-dark nav-link px-1 py-0" href=" "><i class="fa fa-home"
                                        aria-hidden="true"></i> الرئيسيه </a></li>
                            <li class="active mx-2"> المشرفين </a></li>
                        </ol>
                    </div>

                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table-bordered table-striped mg-b-0 text-md-nowrap table p-0 text-center">
                            <thead>
                                <tr>
                                    <th> الاسم</th>
                                    {{-- <th> البريد الاكتروني</th> --}}

                                    <th>الوظيفه</th>
                                    <th>الصلاحيات</th>
                                    <th>الاجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($users)
                                    @foreach ($users as $user)
                                        <tr>
                                            <th scope="row">{{ $user->name }}</th>
                                            <th>
                                                @if ($user->permissions->count() > 0)
                                                    @foreach ($user->roles as $item)
                                                        <span> {{ $item->display_name }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted"> لا توجد وظيفه </span>
                                                @endif
                                            </th>
                                            <th>
                                                @if ($user->permissions->count() > 0)
                                                    @foreach ($user->permissions as $permission)
                                                        <span class="badge bg-secondary">{{ $permission->display_name }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted"> لا توجد صلاحيات مباشرة </span>
                                                @endif
                                            </th>

                                            <th>

                                                @if (auth()->user()->hasPermission("users_update"))
                                                    <button type="button" class="btn btn-sm btn-primary editUserBtn m-2"
                                                        data-id="{{ $user->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#editUserModal">
                                                        <i class="fa fa-edit mx-2" aria-hidden="true"></i> تعديل
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-primary disabled m-2">
                                                        <i class="fa fa-edit mx-2" aria-hidden="true"></i> تعديل
                                                    </button>
                                                @endif
                                                @if (auth()->user()->hasPermission("users_delete"))
                                                    <form action="{{ route("users.destroy", $user->id) }}" method="POST"
                                                        class="d-inline">
                                                        {{ csrf_field() }}
                                                        {{ method_field("delete") }}
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i
                                                                class="fa fa-trash mx-1" aria-hidden="true"></i> حذف </button>
                                                    </form>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-danger disabled">
                                                        <i class="fa fa-trash mx-1" aria-hidden="true"></i> حذف
                                                    </button>
                                                @endif

                                            </th>

                                        </tr>
                                    @endforeach
                                @endisset

                            </tbody>
                        </table>

                        {{-- {{$user->links()}} --}}
                    </div><!-- bd -->
                    {!! $users->appends(request()->search)->links() !!}
                </div><!-- bd -->
            </div><!-- bd -->
        </div>

    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">تعديل مشرف</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form  id="editUserForm" method="POST">
                    @csrf
                    @method("PUT")
                    <div class="modal-body">
                        <input type="hidden" id="edit-user-id" name="user_id">

                        <div class="row mb-3">
                            <label for="edit-name" class="col-md-2 col-form-label text-md-end">اسم المشرف</label>
                            <div class="col-md-6">
                                <input id="edit-name" type="text" class="form-control" name="name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="edit-roles" class="col-md-2 col-form-label text-md-end">الوظيفه</label>
                            <div class="col-md-6">
                                <select class="form-control" name="roles" id="edit-roles">
                                    <option disabled value="" selected>اختر الوظيفه</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="edit-password" class="col-md-2 col-form-label text-md-end">كلمة السر</label>
                            <div class="col-md-6">
                                <input id="edit-password" type="password" class="form-control" name="password"
                                    placeholder="اتركها فارغةاذا لا تريد تغيير كلمة السر">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="edit-password-confirm" class="col-md-2 col-form-label text-md-end">تاكيد كلمة
                                السر</label>
                            <div class="col-md-6">
                                <input id="edit-password-confirm" type="password" class="form-control"
                                    name="password_confirmation">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-md-2 col-form-label text-md-end">الصلاحيات</label>
                            <div class="col-md-10">
                                <ul class="nav nav-tabs" id="editPermissionsTab" role="tablist">
                                </ul>
                                <div class="tab-content mt-2" id="editPermissionsContent">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        <button type="submit" class="btn btn-primary">تعديل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">اضافة مشرف</h5>

                </div>
                <div class="modal-body">
                    <form id="createUserForm">
                        @csrf
                        <div class="row mb-3">
                            <label for="name" class="col-md-2 col-form-label text-md-end">اسم المشرف</label>

                            <div class="col-md-6">
                                <input id="name" type="text"
                                    class="form-control @error("name") is-invalid @enderror" name="name" required
                                    autocomplete="name" autofocus>

                                @error("name")
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="roles" class="col-md-2 col-form-label text-md-end">الوظيفه</label>
                            <div class="col-md-6">
                                <div class="mb-3">

                                    <select class="form-control @error("roles") is-invalid @enderror" name="roles">
                                        <option disabled value="" selected>اختر الوظيفه</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </select>
                                    @error("roles")
                                        <span class="text-danger" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-2 col-form-label text-md-end">كلمة السر</label>

                            <div class="col-md-6">
                                <input id="password" type="password"
                                    class="form-control @error("password") is-invalid @enderror" name="password" required
                                    autocomplete="new-password">

                                @error("password")
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-2 col-form-label text-md-end">تاكيد كلمة
                                السر</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-2 ol-form-label text-md-end">الصلاحيات</label>
                            <div class="col-md-9 p-0">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs p-0" id="myTab" role="tablist">
                                    @php
                                        $groupedPermissions = [];
                                        foreach ($permissions as $permission) {
                                            $parts = explode("_", $permission->name);
                                            $module = $parts[0];
                                            $action = $parts[1];
                                            if (!isset($groupedPermissions[$module])) {
                                                $groupedPermissions[$module] = [];
                                            }
                                            $groupedPermissions[$module][] = [
                                                "action" => $action,
                                                "id" => $permission->id,
                                                "name" => $permission->name
                                            ];
                                        }
                                    @endphp
                                    @foreach (array_keys($groupedPermissions) as $index => $module)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $index == 0 ? "active" : "" }}"
                                                id="home-tab{{ $index }}" data-bs-toggle="tab"
                                                data-bs-target="#home{{ $index }}" type="button" role="tab"
                                                aria-controls="home{{ $index }}"
                                                aria-selected="{{ $index == 1 ? "true" : "false" }}">
                                                {{ __("trans." . $module) }} </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content pe-2">
                                    @foreach (array_keys($groupedPermissions) as $index => $module)
                                        <div class="tab-pane {{ $index == 0 ? "active" : "" }}"
                                            id="home{{ $index }}" role="tabpanel"
                                            aria-labelledby="home-tab{{ $index }}">
                                            <div class="row justify-content-sm-between">
                                                @foreach ($groupedPermissions[$module] as $perm)
                                                    <div class="col-3 p-0">
                                                        <div class="form-check p-0">
                                                            <label style="font-size: 13px;" class="form-check-label">
                                                                <input class="form-check-input" name='permissions[]'
                                                                    type="checkbox"
                                                                    {{ auth()->user()->hasPermission($perm["name"]) ? "" : "disabled" }}
                                                                    value="{{ $perm["id"] }}" />
                                                                {{ __("trans." . $perm["action"]) }}
                                                                {{ __("trans." . $module) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"
                                {{ auth()->user()->hasPermission("users_create") ? "" : "disabled" }}>اضافة</button>
                        </div>

                    </form>
                </div>
 
            </div>
        </div>
    </div>
@endsection
