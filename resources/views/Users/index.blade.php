@extends('layouts.app')
@section('title', 'المشرفين')
@section('content')
    <div class="row row-sm">
        <!--/div-->
        <!--div-->
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header  py-0">

                    <div class="d-flex justify-content-between">
                        <h3 class=" my-2 me-a"> المشرفين <small>{{$users->total()}}</small></h3>
                        <form class="row g-3 h-25 mt-1  needs-validation" action="{{ route('users.index') }}" method="get">
                            <div class="col-md-6 m-0">
                                <input type="text" class="form-control  " value="{{request()->search}}" id="validationCustom01" name="search">

                            </div>
                            <div class="col-md-6 m-0">
                                <button class="btn px-1 btn-primary" type="submit"><i class="fa mx-1 fa-search"
                                        aria-hidden="true"></i>بحث</button>
                                        {{-- @if (auth()->user()->hasPermission('users_create')) --}}
                                        <a class="btn btn-primary my-0 ms-a"href="{{ route('users.create') }}">اضافة مشرف</a>
                                    {{-- @else --}}
                                    {{-- <a class="btn btn-primary my-0 disabled ms-a"href="{{ route('users.create') }}">اضافة مشرف</a> --}}

                                    {{-- @endif/ --}}


                            </div>
                        </form>
                        <ol class="breadcrumb my-2">
                            <li><a class="py-0 text-dark nav-link px-1" href=" "><i
                                        class="fa fa-home" aria-hidden="true"></i> الرئيسيه </a></li>
                            < <li class="active mx-2"> المشرفين </a></li>
                        </ol>
                    </div>

                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered  text-center table-striped mg-b-0 p-0 text-md-nowrap">
                            <thead>
                                <tr>
                                    <th> الاسم</th>
                                    {{-- <th> البريد الاكتروني</th> --}}

                                    <th>الوظيفه</th>
                                    {{-- <th>الحاله</th> --}}
                                    <th>الاجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($users)
                                    @foreach ($users as $user)
                                        <tr>
                                            <th scope="row">{{ $user->name }}</th>
                                            @foreach ($user->roles as $item)
                                            <th> {{$item->name}}</th>
                                            @endforeach

                                            <th>


                                                @if (auth()->user()->hasPermission('users_update'))
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                        class="btn btn-sm  m-2 btn-primary"><i class="fa fa-edit mx-2"
                                                            aria-hidden="true"></i> تعديل</a>
                                                @else
                                                    <a href="{{ route('users.edit',$user->id) }}"
                                                        class="btn btn-sm  m-2 btn-primary disabled"><i class="fa fa-edit mx-2"
                                                            aria-hidden="true"></i> تعديل</a>
                                                @endif
                                                @if (auth()->user()->hasPermission('users_delete'))
                                                    <form action="{{ route('users.destroy',$user->id) }}" method="POST"
                                                        class="d-inline">
                                                        {{ csrf_field() }}
                                                        {{ method_field('delete') }}
                                                        <button type="submit" class="btn btn-sm btn-outline-danger "><i class="fa fa-trash mx-1" aria-hidden="true"></i> حذف </button>
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
        <!--/div-->

        <!--div-->
        {{-- {!! $MainCategories->links() !!} --}}

    </div>
@stop
