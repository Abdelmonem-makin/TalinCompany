@extends("layouts.app")
@section("title", "تعديل مشرف")
@section("content")
    <div class="card">
        <div class="card-header py-0">

            <div class="d-flex justify-content-between">
                <h3 class="me-a my-2"> المشرفين </h3>

                <ol class="breadcrumb my-2">
                    <a class="text-dark nav-link py-0" href="{{ route("home") }}"><i class="fa fa-home"
                            aria-hidden="true"></i> الرئيسيه </a></li>
                    <a class="nav-link text-dark py-0" href="{{ route("users.index") }}"> المشرفين </a></li>
                    <li class="active mx-2">تعديل مشرف</li>
                </ol>

            </div>
        </div>

        @if (Session::has('success'))
            <div class="row mb-3">
                <div class="alert col-md-12 row alert-success w-25" role="alert">
                    <p class="text-center ">{{ Session::get('success') }}</p>
                </div>
            </div>
        @elseif (Session::has('error'))
            <div class="alert alert-success" role="alert">
                <p class="text-center ">{{ Session::get('error') }}</p>
            </div>
        @endif

        <div class="card-body mt-auto">
            <form method="POST" action="{{ route("users.update", $user->id) }}">
                @csrf
                @method("PATCH")
                <div class="row mb-3">
                    <label for="name" class="col-md-2 col-form-label text-md-end">اسم المشرف</label>

                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control @error("name") is-invalid @enderror"
                            name="name" value="{{ $user->name }}" required autocomplete="name" autofocus>

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
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
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
                        <input id="password" type="password" class="form-control @error("password") is-invalid @enderror"
                            name="password" autocomplete="new-password" placeholder="اتركها فارغةاذا لا تريد تغيير كلمة السر">

                        @error("password")
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password-confirm" class="col-md-2 col-form-label text-md-end">تاكيد كلمة السر</label>

                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                            autocomplete="new-password">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="password-confirm" class="col-md-2 col-form-label text-md-end">الصلاحيات</label>
                    <div class="col-md-9 p-0">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs p-0" id="myTab" role="tablist">
                            @php
                                $groupedPermissions = [];
                                foreach ($permissions as $permission) {
                                    $parts = explode('_', $permission->name);
                                    $module = $parts[0];
                                    $action = $parts[1];
                                    if (!isset($groupedPermissions[$module])) {
                                        $groupedPermissions[$module] = [];
                                    }
                                    $groupedPermissions[$module][] = ['action' => $action, 'id' => $permission->id, 'name' => $permission->name];
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
                                <div class="tab-pane {{ $index == 0 ? "active" : "" }}" id="home{{ $index }}"
                                    role="tabpanel" aria-labelledby="home-tab{{ $index }}">
                                    <div class="row justify-content-sm-between">
                                        @foreach ($groupedPermissions[$module] as $perm)
                                            <div class="col-3 p-0">
                                                <div class="form-check p-0">
                                                    <label style="font-size: 13px;" class="form-check-label">
                                                        <input class="form-check-input"
                                                            name='permissions[]' type="checkbox"
                                                            {{ $user->hasPermission($perm['name']) ? 'checked' : '' }}
                                                            {{ auth()->user()->hasPermission($perm['name']) ? '' : 'disabled' }}
                                                            value="{{ $perm['id'] }}" />
                                                        {{ __("trans." . $perm['action']) }} {{ __("trans." . $module) }}
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
      

                <div class="row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary" {{ auth()->user()->hasPermission('users_update') ? '' : 'disabled' }}>
                            تعديل مستخدم
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
