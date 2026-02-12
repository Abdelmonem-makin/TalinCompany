@extends("layouts.app")
@section("title", "اضافة مشرف")
@section("content")
    <div class="card">
        <div class="card-header py-0">

            <div class="d-flex justify-content-between">
                <h3 class="me-a my-2"> المشرفين </h3>

                <ol class="breadcrumb my-2">
                    <a class="text-dark nav-link py-0" href="{{ route("users.index") }}"><i class="fa fa-home"
                            aria-hidden="true"></i> الرئيسيه </a></li>
                    < <a class="nav-link text-dark py-0" href="{{ route("users.index") }}"> المشرفين </a></li>
                        < <li class="active mx-2">اضافة مشرف</li>
                </ol>

            </div>
        </div>

        <div class="card-body mt-auto">
            <form method="POST" action="{{ route("users.store") }}">
                @csrf
                <div class="row mb-3">
                    <label for="name" class="col-md-2 col-form-label text-md-end">اسم المشرف</label>

                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control @error("name") is-invalid @enderror"
                            name="name" value="{{ old("name") }}" required autocomplete="name" autofocus>

                        @error("name")
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
      
                <div class="row mb-3">
                    <label for="roles" class="col-md-2 col-form-label text-md-end">الأدوار</label>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <select id="roles" class="form-control @error("roles") is-invalid @enderror" name="roles[]" multiple>
                                <option disabled value="">اختر الأدوار</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>{{ $role->display_name }}</option>
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
                            name="password" required autocomplete="new-password">

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
                            required autocomplete="new-password">
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
                        <button type="submit" class="btn btn-primary">
                            {{ __("Register") }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
