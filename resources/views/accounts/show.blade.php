@extends("layouts.app")
@section("content")

    <div class="mb-3">
        {{-- <a href="{{ route("accounts.index") }}" class="btn btn-secondary">رجوع</a> --}}

        @if ($account->kind === "receipt")
     <h1>  سندات قبض حساب العميل : {{ $account->name }} </h1>
        @elseif($account->kind === "payment")
    <h3 class="mt-4">سندات دفع حساب المورد : {{$account->name}}</h3>
        @endif
    <strong>الرصيد :</strong> {{ $account->balance }}
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
                      
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $transactions->links() }}
    @else
        <p>لا توجد سندات دفع أو قبض لهذا الحساب.</p>
    @endif

@endsection
