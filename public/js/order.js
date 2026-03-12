$(function () {
    $(document).on('click', '.add-product_sales-btn', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var name = $btn.data('name');
        var id = $btn.data('id');
        var price = $btn.data('price');

        var html = '<div id="cart-box-' + id + '" class="cart-shop-item row"> <div class="col-8"><div class="cart-item-title"><h4>' + name +
            '</h4></div><div class="cart-item-price prodcut-price">' + $.number(price, 2) +
            '</div></div><div class="col-4 m-auto text-right"> <input data-price="' + price +
            '" name="products[' + id + '][quantity]" type="number" class="form-control text-center product-quanities m-auto p-0" value="1" min="1"><button type="button" class="btn my-1 px-4 btn-sm remov-prodect-btn btn-danger " data-id="'
            + id + '"><i class="fa fa-trash mx-1" aria-hidden="true"></i></button></div><hr></div> ';
        // avoid adding duplicate item if already in cart
        if ($('#cart-sales-box-' + id).length === 0) {
            $('.cart-sales-shoping').append(html);
            $btn.removeClass('btn-dark').addClass('btn-default disabled');
            $('#add-sales-btn').removeClass('btn-default disabled').addClass('btn-dark');
        }
    }); //اضافة المنتج الى السله المخزن
    // delegated handler so buttons added later still work
    $(document).on('click', '.add-product-btn', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var name = $btn.data('name');
        var id = $btn.data('id');
        var price = $btn.data('price');
        var html = '   <div id="cart-box-' + id + '" class="cart-shop-item row"> <div class="col-8"><div class="cart-item-title"><h4>' + name +
            '</h4></div><div class="cart-item-price prodcut-price">' + $.number(price, 2) +
            '</div></div><div class="col-4 m-auto text-right"> <input data-price="' + price +
            '" name="products[' + id + '][quantity]" type="number" class="form-control text-center product-quanities m-auto p-0" value="1" min="1"><button type="button" class="btn my-1 px-4 btn-sm remov-prodect-btn btn-danger " data-id="'
            + id + '"><i class="fa fa-trash mx-1" aria-hidden="true"></i></button></div><hr></div> ';
        // avoid adding duplicate item if already in cart
        if ($('#cart-box-' + id).length === 0) {
            $('.cart-shoping').append(html);
            calculat();
            $btn.removeClass('btn-dark').addClass('btn-default disabled');
        }
    });//اضافة المنتج الى السله
    $('body').on('click', '.disabled', function (e) {
        e.preventDefault();
    });

    //هنا يتم الضغط على ذر عرض الطلبات يتم عرض المنتجات الخاصه بالطلب
    $('.Show-product').on('click', function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var method = $(this).data('method');
        $.ajax({
            url: url,
            method: method,
            success: function (data) {
                //هنا يتم تفريغ قائمة الطلبات و رض المنتجات التى تم تحديدها
                $('.list-order-product').empty();

                $('.list-order-product').append(data);
            }
        })
    });
    $('body').on('click', '.remov-prodect-btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#cart-sales-box-' + id).remove();
        $('#product-sales' + id).removeClass('btn-default disabled').addClass('btn-dark');
        calculat();
    });//حذف العنصر من السله
    $('body').on('click', '.remov-prodect-btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#cart-box-' + id).remove();
        $('#product-' + id).removeClass('btn-default disabled').addClass('btn-dark');
        calculat();
    });//حذف العنصر من السله
    $('body').on('click', '#add-order-btn', function () {
        // When user clicks the visual button ensure it's enabled so the form can submit
        $('#add-order-btn').removeClass('disabled').prop('disabled', false);
    });//حذف العنصر من السله


    $('body').on('change', '.product-quanities', function () {
        var quanities = parseInt($(this).val());
        var price = parseInt($(this).data('price'));
        var totl = quanities * price;
        $(this).closest('.cart-shop-item').find('.prodcut-price').html($.number(totl, 2));
        calculat();
    });//حصاب قمة عدد من المنتجات و ايجاد  و قيمتها
    $(document).on('click', '.print-order-btn', function (e) {
        e.preventDefault();
        $('#order-list').printThis();
    });   //لطباعة فاتورة
    $(document).on('click', '.print-order-bill', function (e) {
        e.preventDefault();
        $('#print-bill').printThis();
    });
});


function calculat() {
    var price = 0;

    $('.cart-shop-item .prodcut-price').each(function (index) {
        price += parseFloat($(this).html().replace(/,/g, ''));

    });
    // console.log($.number(price,2));
    $('.total-price').html($.number(price, 2));
    if (price > 0) {
        // Ensure the button is both visually enabled and actually clickable
        $('#add-order-btn').removeClass('disabled').prop('disabled', false);
    } else {
        // Visual disabled state and disable click
        $('#add-order-btn').addClass('disabled').prop('disabled', true);
    }


}//دالة حستب اجمال قمة المبيعات
