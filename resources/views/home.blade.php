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
                    <p class="display-6 fw-bold">$12,450</p>
                    <small class="text-muted">زيادة 5% عن الأمس</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card p-3 h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-success">المخزون المتاح</h5>
                    <p class="display-6 fw-bold">8,320 عبوة</p>
                    <small class="text-muted">من 15 صنف</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card p-3 h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-danger">فواتير مستحقة</h5>
                    <p class="display-6 fw-bold">$3,240</p>
                    <small class="text-muted">3 فواتير</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card p-3 h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-warning">أصناف قاربت الانتهاء</h5>
                    <p class="display-6 fw-bold">12</p>
                    <small class="text-muted">تحتاج إعادة طلب</small>
                </div>
            </div>
        </div>
    </section>

    <section class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card p-3 shadow-sm">
                <h6 class="card-title">أعلى العملاء مبيعًا</h6>
                <ol class="mb-0">
                    <li>مستشفى الرحمة - $12,000</li>
                    <li>صيدلية النيل - $9,500</li>
                    <li>موزع الشرق - $7,200</li>
                    <li>عيادة الأمل - $5,800</li>
                    <li>مركز الطبي - $4,200</li>
                </ol>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card p-3 shadow-sm">
                <h6 class="card-title">أحدث المعاملات</h6>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        فاتورة رقم 1234
                        <span class="badge bg-success rounded-pill">$500</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        شراء من مورد ABC
                        <span class="badge bg-primary rounded-pill">$1,200</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        دفع راتب للموظف X
                        <span class="badge bg-warning rounded-pill">$800</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        فاتورة رقم 1235
                        <span class="badge bg-success rounded-pill">$300</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>
@endsection
