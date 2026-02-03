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

               <div class="mb-4 text-center">
                <p><strong>شركة تالين الطبيه</strong></p>

            <h2>فاتورة مشتريات</h2>
                   <span>رقم الفاتوره: {{ $sales->invoice_number }}</span><br>
                <span>تاريخ الطلب: {{ $sales->date->format("Y-m-d") }} </span><br>
            
          
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>اسم العميل:</strong> <span id="modal-supplier">{{  optional($sales->customer)->name ? optional($sales->customer)->name : "عميل افتراضي"  }}</span></p>

                <span> عدد الطلبات: {{ $sales->item->count() }}</span>
            </div>
            <div class="col-md-6 text-end">
            
            </div>
        </div>
             
                @foreach ($sales_pro as $product)
                    <tr>
                        <th scope="row">{{ $product->name }}</th>
                        <th scope="row">{{ $product->pivot->stock }}</th>
                        <th scope="row">{{ number_format($product->pivot->sales_price) }}</th>

                    </tr>
                @endforeach

            </tbody>
          <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">اجمال المبيلغ الكلي:</th>
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
