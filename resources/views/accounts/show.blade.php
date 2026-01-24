@extends("layouts.app")
@section("content")
    <h1>
        حساب العميل : {{ $account->name }}</h1>
    <ul class="i navbar-nav ">
        <li class="nav-item"><strong>رقم الحساب:</strong> {{ $account->number }}</li>
        <li class="nav-item"><strong> نوع الحساب :</strong> {{__("trans." . $account->type)}}</li>
        <li class="nav-item"><strong>الرصيد :</strong> {{ $account->balance }}</li>
    </ul>
    <div class="mb-3">
        {{-- <a href="{{ route("accounts.index") }}" class="btn btn-secondary">رجوع</a> --}}

        @if ($account->kind === "receipt")
                   
    <h3 class="mt-4">سندات القبض    </h3>

        @elseif($account->kind === "payment")
                    
    <h3 class="mt-4">سندات     الدفع </h3>

        @endif

    </div>


    @if (isset($transactions) && $transactions->count())
        <table class="text-bold table-striped table">
            <thead >
                <tr>
                    <th>التاريخ</th>
                    <th>النوع</th>
                    {{-- <th>المبلغ</th> --}}
                    <th >دائن</th>
                    <th>مدين</th>
                    <th>الوصف</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $t)
                    <tr>
                        <td>{{ $t->date ?? $t->created_at->format("Y-m-d") }}</td>
                        <td>
                            @if ($t->kind === "receipt")
                                قبض
                            @elseif($t->kind === "payment")
                                دفع
                            @endif
                        </td>
                        <td class="text-success">
                            @if ($t->type === "debit")
                                {{ $t->amount }}+
                            @else
                                --------
                            @endif
                        </td>
                        <td class="text-danger">
                            @if ($t->type === "credit")
                                {{ $t->amount }}-
                            @else
                                --------
                            @endif
                        </td>
                        {{-- <td>{{ $account->type == "credit" ?  $account->amount : -}}</td> --}}
                        {{-- <td>{{ $t->type }}</td> --}}
                        <td>{{ $t->description }}</td>
                        <td><a href="{{ route("transactions.show", $t) }}" class="btn btn-sm btn-outline-secondary">عرض</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $transactions->links() }}
    @else
        <p>لا توجد سندات دفع أو قبض لهذا الحساب.</p>
    @endif

@endsection
