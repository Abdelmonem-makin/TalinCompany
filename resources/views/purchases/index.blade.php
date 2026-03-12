@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>المشتريات</h1>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">اضافة مورد مشتريات </a>
    </div>
    <table class="table table-striped">
        
        <thead>
            <tr>
                <th>رقم </th>
                <th> اسم المورد</th>
                {{-- <th>التاريخ</th> --}}
                <th>اجمالي المشتريات</th>
                {{-- <th>الحاله</th> --}}
                <th>اجراء</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
            <tr>
                <td>{{ $purchase->id }}</td>
                <td>{{ optional($purchase->supplier)->name }}</td>
                {{-- <td>{{ $purchase->date->format('Y-m-d') }}</td> --}}
                <td>{{ number_format($purchase->total, 2) }}</td>
                {{-- <td>{{ $purchase->status }}</td> --}}
                <td>
                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info">عرض </a>
                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-sm btn-warning">تعديل</a>
                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete purchase?')">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $purchases->links() }}
</div>
@endsection
