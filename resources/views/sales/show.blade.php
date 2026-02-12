<div id="order-list" class="table-responsive order-list text-cenetr col-xl-12">
    @isset($sales_pro)
        <div class="mb-4 text-center">
            <p><strong>شركة تالين الطبيه</strong></p>
            <h2>فاتورة مشتريات</h2>

        </div> <hr>
        <div class="row mb-3">
            <div class="col-md-12">
                <span class="mx-3"><strong>رقم الفاتوره: </strong>{{ $sales->invoice_number }}</span>
                <span class="mx-3"><strong> تاريخ الطلب: </strong>{{ $sales->date->format("Y-m-d") }} </span>
                <span class="mx-3"><strong> اسم العميل:
                    </strong>{{ optional($sales->customer)->name ? optional($sales->customer)->name : "عميل افتراضي" }}</span>
                <span class="mx-3"><strong> عدد الطلبات: </strong>{{ $sales->item->count() }}</span>
            </div>
            <div class="col-md-6 text-end">

            </div>
        </div>
        <table class="table-bordered table-striped mg-b-0 text-md-nowrap mb-2 table p-0 text-center text-right">
            <thead>
                <tr>
                    <th> اسم المنج</th>
                    <th> الكميه</th>
                    <th>سعر</th>
                    <th>اجمالي المبلغ </th>

                </tr>
            </thead>
            <tbody>
                @foreach ($sales_pro as $product)
                    <tr>
                        <th scope="row">{{ $product->name }}</th>
                        <th scope="row">{{ $product->pivot->stock }}</th>
                        <th scope="row">{{ $product->price }}</th>
                        <th scope="row">{{ number_format($product->pivot->sales_price) }}</th>

                    </tr>
                @endforeach

            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">اجمال المبيلغ الكلي:</th>
                    <th id="modal-total"> SD {{ number_format($sales->total, 2) }} </th>
                </tr>
            </tfoot>
        </table>
        <div class="mt-4 text-center">
            <p>شكراً لتعاملكم معنا</p>

        </div>
    @endisset

</div>

</div>

<!--  Modal trigger button  -->
