<!doctype html>
<html dir="rtl" lang="{{ str_replace("_", "-", app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config("app.name", "Laravel") }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset("css/font-awesome.min.css") }}">
    <STYle>
        .nav-link {
            color: white;
        }

        .alert {
            position: absolute;
        }
    </STYle>
    <!-- Scripts -->
    @vite(["resources/sass/app.scss", "resources/js/app.js"])
</head>

<body class="" style="font-size: 1.05rem;">
    <div id="app">
        @auth

            <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ route("home") }}">
                        {{-- {{ config('app.name', 'Laravel') }} --}}
                        تالين
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __("Toggle navigation") }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="navbar-collapse collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->

                        <!-- Right Side Of Navbar -->
                        <!-- Authentication Links -->
                        {{-- <ul class="navbar-nav me-auto">

                            @if (Route::has("login"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route("login") }}">{{ __("Login") }}</a>
                                </li>
                            @endif

                            @if (Route::has("register"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route("register") }}">{{ __("Register") }}</a>
                                </li>
                            @endif
                    </ul> --}}
                        {{-- @else --}}
                        <ul class="navbar-nav text-light ms-auto">
                            <li class="nav-item"><a class="nav-link" href="{{ route("customers.index") }}">العملاء</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route("suppliers.index") }}">الموردين</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route("items.index") }}">المنتجات</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route("stock.index") }}">المخزون</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route("sales.index") }}">المبيعات
                                    والفواتير</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route("purchases.index") }}">المشتريات </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    الحسابات
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route("accounts.index") }}">الحسابات</a>
                                    <a class="dropdown-item" href="{{ route("employees.index") }}">الموظفين</a>
                                    {{-- <a class="dropdown-item" href="{{ route("payroll-transactions.index") }}">المرتبات</a> --}}

                                    <a class="dropdown-item" href="{{ route("expenses.index") }}">المصروفات</a>
                                    <a class="dropdown-item" href="{{ route("invoices.index") }}">التقارير</a>
                                    {{-- <a class="dropdown-item" href="{{ route('accounts.debts') }}">الديون</a> --}}

                                    {{-- <a class="dropdown-item text-justify" href="{{ route("transactions.index") }}">
                                        المعاملات
                                    </a> --}}

                                    <a class="dropdown-item text-justify" href="{{ route("accounts.debts") }}">
                                        تقرير الديون
                                    </a>
                                </div>
                            </li>

                        </ul>
                        <ul class="navbar-nav me-auto">
                            <!-- Notifications Dropdown -->
                        
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle position-relative" href="#"
                                    id="notificationsDropdown" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    @if ($expiredStocks->count() + $expiringSoonStocks->count() > 0)
                                        <span
                                            class="position-absolute start-100 translate-middle badge rounded-pill bg-danger top-0">
                                            {{ $expiringSoonStocks->count() +$expiredStocks->count() }}
                                            <span class="visually-hidden">تنبيهات المخزون</span>
                                        </span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="notificationsDropdown"
                                    style="min-width: 350px;">
                                    @if ($expiringSoonStocks->count() > 0)
                                        <li>
                                            <h6 class="dropdown-header text-info"><i class="fas fa-clock me-2"></i>تنتهي
                                                خلال 7 أيام</h6>
                                        </li>
                                        @foreach ($expiringSoonStocks->take(3) as $stock)
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route("stock.index") }}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="fw-bold">{{ $stock->item->name ?? "غير محدد" }}</span>
                                                        <small class="text-muted">{{ $stock->quantity }} قطعة</small>
                                                    </div>
                                                    <small class="text-info">
                                                        تنتهي في:
                                                        {{ $stock->expiry ? $stock->expiry->format("Y-m-d") : "غير محدد" }}
                                                        @if ($stock->expiry)
                                                            ({{ now()->diffInDays($stock->expiry) }} يوم)
                                                        @endif
                                                    </small>
                                                </a>
                                            </li>
                                        @endforeach
                                        @if ($expiringSoonStocks->count() > 3)
                                            <li>
                                                <p class="text-muted mb-1 text-center">و {{ $expiringSoonStocks->count() - 3 }} منتج
                                                    آخر...</p>
                                            </li>
                                        @endif
                                        @if ($expiredStocks->count() +  $expiringSoonStocks->count() > 0)
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                        @endif
                                    @endif

                                    @if ($expiredStocks->count() > 0)
                                        <li>
                                            <h6 class="dropdown-header text-danger"><i
                                                    class="fas fa-exclamation-triangle me-2"></i>منتجات منتهية الصلاحية</h6>
                                        </li>
                                        @foreach ($expiredStocks->take(3) as $stock)
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route("stock.index") }}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="fw-bold">{{ $stock->item->name ?? "غير محدد" }}</span>
                                                        <small class="text-muted">{{ $stock->quantity }} قطعة</small>
                                                    </div>
                                                    <small class="text-danger">
                                                        انتهت في:
                                                        {{ $stock->expiry ? $stock->expiry->format("Y-m-d") : "غير محدد" }}
                                                    </small>
                                                </a>
                                            </li>
                                        @endforeach
                                        @if ($expiredStocks->count() > 3)
                                            <li>
                                                <p class="text-muted mb-1 text-center">و {{ $expiredStocks->count() - 3 }}
                                                    منتج آخر...</p>
                                            </li>
                                        @endif
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                    @endif

                                    @if ($expiredStocks->count()+  $expiringSoonStocks->count() == 0)
                                        <li>
                                            <h6 class="dropdown-header text-success"><i
                                                    class="fas fa-check-circle me-2"></i>جميع المنتجات سليمة</h6>
                                        </li>
                                        <li>
                                            <p class="text-muted mb-1 text-center">لا توجد منتجات منتهية أو قريبة من
                                                الانتهاء</p>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                    @endif

                            
                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route("logout") }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __("Logout") }}
                                    </a>

                                    <form id="logout-form" action="{{ route("logout") }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                        </ul>
                    </div>
                </div>
            </nav>
        @endauth

        <main class="container py-4" style="padding-top: 80px;">
            @includeIf("partials.alerts")
            {{-- <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
                <!-- Toast للنجاح -->
                @if (session("success"))
                    <!-- Toast Container -->
                    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert"
                        aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                {{ session("success") }}
                            </div>
                            <button type="button" class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"
                                aria-label="إغلاق"></button>
                        </div>
                    </div>
                @endif

                <!-- Toast للفشل -->
                <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ❌ فشل في حفظ المنتج
                        </div>
                        <button type="button" class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"
                            aria-label="إغلاق"></button>
                    </div>
                </div>
            </div> --}}

            {{-- <div class="alert alert-success">{{ session("success") }}</div> --}}
            @yield("content")
        </main>
    </div>
    <script src="{{ asset("js/jquery-3.5.1.min.js") }}"></script>
    {{-- <script src="{{ asset('js/bootstrap.min.js') }}"></script> --}}
    <script src="{{ asset("js/order.js") }}"></script>
    <script src="{{ asset("js/modal.js") }}"></script>
    <script src="{{ asset("js/printThis.js") }}"></script>
    <script src="{{ asset("js/jquery.number.min.js") }}"></script>
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
    @yield("scripts")

</body>

</html>
