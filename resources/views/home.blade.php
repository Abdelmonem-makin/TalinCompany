@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">لوحة تحكم  </h1>
        <div>
            <a href="#" class="btn btn-outline-secondary me-2">الإعدادات</a>
            <a href="{{ route('logout') }}" class="btn btn-success" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">تسجيل خروج</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <section class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card p-3 h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary">مبيعات اليوم</h5>
                    <p class="display-6 fw-bold">{{ number_format($todaySales, 2) }}</p>
                    <small class="text-muted">مجموع الفواتير المسجلة اليوم</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card p-3 h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-success">المخزون المتاح</h5>
                    <p class="display-6 fw-bold">{{ number_format($totalInventory) }} وحدة</p>
                    <small class="text-muted">عدد إجمالي الوحدات في المخزون</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card p-3 h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-danger">فواتير مستحقة</h5>
                    <p class="display-6 fw-bold">{{ $pendingInvoices }}</p>
                    <small class="text-muted">فواتير بوضعية انتظار</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card p-3 h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-warning">أصناف منخفضة المخزون</h5>
                    <p class="display-6 fw-bold">{{ $lowStockItems }}</p>
                    <small class="text-muted">أصناف مخزونها ≤ 10</small>
                </div>
            </div>
        </div>
    </section>

    <div class="mb-4 d-flex gap-2">
        <a href="{{ route('sales.index') }}" class="btn btn-primary">إنشاء فاتورة</a>
        <a href="//{{ route('purchases.index') }}" class="btn btn-secondary">إضافة مشتريات</a>
        <a href="{{ route('items.index') }}" class="btn btn-outline-primary">عرض الأصناف</a>
        <a href="{{ route('stock.index') }}" class="btn btn-outline-warning">عرض المخزون</a>
    </div>

    <section class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card p-3 shadow-sm">
                <h6 class="card-title">أعلى العملاء مبيعًا</h6>
                <ol class="mb-0">
                    @forelse($topCustomers as $customer)
                        <li>{{ $customer->name }} - ${{ number_format($customer->sales_sum_total ?? 0, 2) }}</li>
                    @empty
                        <li class="text-muted">لا توجد بيانات</li>
                    @endforelse
                </ol>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card p-3 shadow-sm">
                <h6 class="card-title">أحدث المعاملات</h6>
                <ul class="list-group list-group-flush">
                    @forelse($recentTransactions as $transaction)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $transaction->description ?? 'معاملة رقم ' . $transaction->id }}
                            <span class="badge bg-{{ $transaction->kind === 'credit' ? 'success' : 'primary' }} rounded-pill">
                                ${{ number_format($transaction->amount, 2) }}
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">لا توجد معاملات حديثة</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </section>
</div>
@endsection
