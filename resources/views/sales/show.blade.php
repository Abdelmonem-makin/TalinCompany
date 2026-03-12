<div id="order-list" class="table-responsive order-list text-cenetr col-xl-12">
    <table class="table-bordered table-striped mg-b-0 text-md-nowrap mb-2 table p-0 text-center text-right">
        <thead>
            <tr>
                <th> اسم المنج</th>
                <th> الكميه</th>

                {{-- <th> </th> --}}
                <th>السعر</th>
            </tr>
        </thead>
        <tbody>
            @isset($sales_pro)
                <span>رقم الفاتوره: {{ $sales->invoice_number }}</span><br>
                <span>تاريخ الطلب: {{ $sales->date->format("Y-m-d") }} </span><br>
                <span> عدد الطلبات: {{ $sales->item->count() }}</span>
                @foreach ($sales_pro as $product)
                    <tr>
                        <th scope="row">{{ $product->name }}</th>
                        <th scope="row">{{ $product->pivot->stock }}</th>
                        <th scope="row">{{ number_format($product->pivot->sales_price) }}</th>

                    </tr>
                @endforeach

            </tbody>

        </table>
        <div class="d-flex justify-content-between">
            <div>اجمالي المبلغ:</div>
            <div class="total-price">SD {{ number_format($sales->total, 2) }} </div>
        </div>
    @endisset

</div>

</div>
<!--  Modal trigger button  -->
