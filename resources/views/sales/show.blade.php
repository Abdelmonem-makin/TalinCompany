    @isset($sales_pro)
        <div class="mb-4 text-center">
            <p><strong>شركة تالين الطبيه</strong></p>
            <h2>فاتورة مبيعات</h2>

        </div>
        <hr>
        <div class="row mb-3">
            <div class="col-md-12">
                <span class="ms-3"><strong>رقم الفاتوره: </strong>{{ $sales->invoice_number }}</span>
                <span class="ms-3"><strong> تاريخ الطلب: </strong>{{ $sales->date->format("Y-m-d") }} </span>
                <span class="ms-3"><strong> اسم العميل:
                    </strong>{{ optional($sales->customer)->name ? optional($sales->customer)->name : "عميل افتراضي" }}</span>
                <span class="ms-3"><strong> عدد الطلبات: </strong>{{ $sales->item->count() }}</span>
            </div>
            <div class="col-md-6 text-end">

            </div>
        </div>
        <div  class="table-responsive order-list text-cenetr">

            <table class="table-bordered table-striped  text-md-nowrap mb-2 table p-0 text-center text-right">
            <thead>
                <tr>
                    <th> اسم المنج</th>
                    <th>  الوحده</th>
                    <th> الكميه</th>
                    <th> الصلاحيه</th>
                    <th> سعر الوحده</th>
                    <th>اجمالي المبلغ </th>

                </tr>
            </thead>
            <tbody>
                @foreach ($sales_pro as $product)
                    @php
                        // collect stock movements (sales stock entries) for this sale and item
                        $movements = $sales->Stock->where('item_id', $product->id);
                        // get unique batch ids used for this item in this sale
                        $batchIds = $movements->pluck('batch_id')->filter()->unique();
                        // map to expiry dates (if batch exists)
                        $batchExpiries = $batchIds->map(function($bId) {
                            $batch = \App\Models\Stock::find($bId);
                            return $batch ? $batch->expiry : null;
                        })->filter();
                        // fallback expiry (next available) when no batch info
                        $fallbackExpiry = $product->next_expiry;
                        $isExpired = false;
                        foreach ($batchExpiries as $be) {
                            if ($be && $be->lte(now())) { $isExpired = true; break; }
                        }
                    @endphp

                    <tr >
                        <th scope="row">{{ $product->name }}
                            {{-- @if($isExpired)
                                <span class="badge bg-danger ms-2">منتهي الصلاحية</span>
                            @endif --}}
                        </th>
                        <th scope="row">{{ $product->type }}</th>
                        <th scope="row">{{ $product->pivot->stock }}</th>
                        <th scope="row">
                            @if($batchExpiries->isNotEmpty())
                                @foreach($batchExpiries as $bexp)
                                    {{ $bexp ? $bexp->format('Y-m-d') : 'غير محدد' }}@if(! $loop->last), @endif
                                @endforeach
                            @elseif($fallbackExpiry)
                                {{ $fallbackExpiry->format('Y-m-d') }}
                            @else
                                غير محدد
                            @endif
                        </th>
                        <th scope="row">{{ $product->price }}</th>
                        <th scope="row">{{ number_format($product->pivot->sales_price) }}</th>
                    </tr>
                @endforeach

            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">اجمال المبلغ الكلي:</th>
                    <th id="modal-total"> SD {{ number_format($sales->total, 2) }} </th>
                </tr>
            </tfoot>
        </table>
        </div>

        <div class="mt-4 text-center">
            <p>شكراً لتعاملكم معنا</p>

        </div>
    @endisset

    </div>

    <!--  Modal trigger button  -->
