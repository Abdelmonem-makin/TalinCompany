@extends('layouts.app')

@section('content')
<div class="container">
    <h1>تقرير الديون</h1>

    <div class="row">
        <div class="col-md-6">
            <h3>المدينون (عملاء)</h3>
            <table class="table table-striped">
                <thead>
                    <tr><th>اسم العميل</th><th>الرصيد</th><th>إجراء</th></tr>
                </thead>
                <tbody>
                    @foreach($customers as $c)
                        <tr>
                            <td>{{ $c['name'] }}</td>
                            <td>{{ number_format($c['balance'], 2) }}</td>
                            <td><a href="{{ route('customers.show', $c['id']) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h3>الدائنون (موردون)</h3>
            <table class="table table-striped">
                <thead>
                    <tr><th>اسم المورد</th><th>الرصيد</th><th>إجراء</th></tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $s)
                        <tr>
                            <td>{{ $s['name'] }}</td>
                            <td>{{ number_format($s['balance'], 2) }}</td>
                            <td><a href="{{ route('suppliers.show', $s['id']) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
