$(function () {
    $('.printBtn').on('click', function () {
        var purchaseId = $(this).data('id');
        var referenceId = $(this).data('reference');
        var supplier = $(this).data('supplier');
        var date = $(this).data('date');
        var total = $(this).data('total');

        // console.log('Print button clicked for purchase:', purchaseId);

        // Fill basic info
        $('#modal-reference-id').text(referenceId);
        $('#modal-supplier').text(supplier);
        $('#modal-date').text(date);
        $('#modal-total').text(total);

        // Fetch lines via AJAX
        $.get('/admin/purchases/' + purchaseId + '/data', function (data) {
            console.log('Received data:', data);
            var linesHtml = '';
            data.lines.forEach(function (line) {
                linesHtml += '<tr>' +
                    '<td>' + line.item_name + '</td>' +
                    '<td>' + line.quantity + '</td>' +
                    '<td>' + line.unit_price + '</td>' +
                    '<td>' + line.total + '</td>' +
                    '</tr>';
            });
            $('#modal-lines').html(linesHtml);
        }).fail(function (xhr, status, error) {
            console.error('AJAX error:', status, error);
            alert('حدث خطأ في جلب البيانات');
        });
    });

    $('#print-modal-btn').on('click', function () {
        $('#invoice-print-content').printThis({
            importCSS: true,
            importStyle: true,
            loadCSS: "{{ asset('css/ app.css') }}",
            pageTitle: "فاتورة مشتريات - " + $('#modal-reference-id').text(),
            removeInline: false,
            printDelay: 333,
            header: null,
            footer: null
                    });
});

// edit Sales Full Modal
$('.editpurchasesFullBtn').on('click', function () {
    var purchaseId = $(this).data('id');
    var supplierId = $(this).data('supplier');

    $('#edit-purchase-id').val(purchaseId);
    $('#edit-supplier-id').val(supplierId);

    // Load products table
    $.get('/admin/items-data', function (items) {
        var tableHtml = '';
        items.forEach(function (item, index) {
            tableHtml += '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + item.name + '</td>' +
                '<td>' + item.stock + '</td>' +
                // '<td>' + item.price + '</td>' +
                '<td><a class="btn btn-dark btn-sm add-product-edit-btn" data-id="' +
                item.id + '" data-name="' + item.name + '" data-price="' + item
                    .price + '" href="#">إضافة</a></td>' +
                '</tr>';
        });
        $('#edit-products-table').html(tableHtml);
    });

    // Load existing cart
    $.get('/admin/purchases/' + purchaseId + '/data', function (data) {
        $('#edit-supplier-id').val(data.supplier_id || '');
        var cartHtml = '';
        data.lines.forEach(function (line) {
            cartHtml += '<div class="row mb-2 product-item" id="edit-product-purchases-' +
                line.item_id + '">' +
                '<div class="col-3"><strong>' + line.item_name +
                '</strong></div>' +
                '<div class="col-3"><input type="number" class="form-control quantity-input" name="products[' +
                line.item_id + '][quantity]" value="' + line.quantity +
                '" min="1" required></div>' +
                '<div class="col-4"><input type="text"   class="form-control unit-price-input" name="products[' +
                line.item_id + '][unit_price]" value="' + line.unit_price + '" min="0" required></div>' +
                '<div class="col-1"><button type="button" class="btn btn-danger btn-sm remove-product-edit" data-id="' +
                line.item_id + '">حذف</button></div>' +
                '</div>';
        });
        $('#edit-order-list').html(cartHtml);
        updateEditTotal();

        // Disable buttons for added products
        $('.product-item').each(function () {
            var id = $(this).attr('id').replace('edit-product-purchases-', '');
            $('.add-product-edit-btn[data-id="' + id + '"]').addClass(
                'disabled').attr('aria-disabled', 'true');
        });
    });


    //      
    $('#editFullForm').attr('action', '/admin/purchases/' + purchaseId + '/update-full');
});

// edit sales Full Modal
$('.editSalesFullBtn').on('click', function () {
    var salesId = $(this).data('id');
    var customerId = $(this).data('customer');

    $('#edit-sales-id').val(salesId);
    $('#edit-customer-id').val(customerId);

    // Load products table
    $.get('/admin/items-data', function (items) {
        var tableHtml = '';
        items.forEach(function (item, index) {
            tableHtml += '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + item.name + '</td>' +
                '<td>' + item.stock + '</td>' +
                '<td>' + item.price + '</td>' +
                '<td><a class="btn btn-dark btn-sm add-product-edit-btn" data-id="' +
                item.id + '" data-name="' + item.name + '" data-price="' + item
                    .price + '" href="#">إضافة</a></td>' +
                '</tr>';
        });
        $('#edit-products-table').html(tableHtml);
    });



    $.get('/admin/sales/' + salesId + '/data', function (data) {
        $('#edit-customer-id').val(customerId || '');
        var cartHtml = '';
        data.lines.forEach(function (line) {
            cartHtml += '<div class="row mb-2 product-item" id="edit-product-purchases-' +
                line.item_id + '">' +
                '<div class="col-3"><strong>' + line.item_name +
                '</strong></div>' +
                '<div class="col-3"><input type="number" class="form-control quantity-input" name="products[' +
                line.item_id + '][quantity]" value="' + line.quantity +
                '" min="1" required></div>' +
                '<div class="col-4"><input type="text"   class="form-control unit-price-input" name="products[' +
                line.item_id + '][unit_price]" value="' + line.unit_price + '" min="0" required></div>' +
                '<div class="col-1"><button type="button" class="btn btn-danger btn-sm remove-product-edit" data-id="' +
                line.item_id + '">حذف</button></div>' +
                '</div>';
        });
        $('#edit-order-list').html(cartHtml);
        updateEditTotal();

        // Disable buttons for added products
        $('.product-item').each(function () {
            var id = $(this).attr('id').replace('edit-product-purchases-', '');
            $('.add-product-edit-btn[data-id="' + id + '"]').addClass(
                'disabled').attr('aria-disabled', 'true');
        });
    });
    // Set form action
    $('#editFullForm').attr('action', '/admin/sales/' + salesId + '/update-sales');
});

// Add product in edit modal
$(document).on('click', '.add-product-edit-btn', function (e) {
    e.preventDefault();
    var $btn = $(this);
    if ($btn.hasClass('disabled')) return;

    var productId = $btn.data('id');
    var productName = $btn.data('name');
    var productPrice = $btn.data('price');

    if ($('#edit-product-' + productId).length > 0) {
        alert('المنتج موجود بالفعل');
        return;
    }

    var productHtml = '<div class="row mb-2 product-item" id="edit-product-' + productId +
        '">' +
        '<div class="col-3"><strong>' + productName + '</strong></div>' +
        '<div class="col-3"><input type="number" class="form-control quantity-input" name="products[' +
        productId + '][quantity]" value="1" min="1" required></div>' +
        '<div class="col-4"><input type="number" step="0.01" class="form-control unit-price-input" name="products[' +
        productId + '][unit_price]" value="' + productPrice + '" min="0" required></div>' +
        '<div class="col-1"><button type="button" class="btn btn-danger btn-sm remove-product-edit" data-id="' +
        productId + '">حذف</button></div>' +
        '</div>';

    $('#edit-order-list').append(productHtml);
    $btn.addClass('disabled').attr('aria-disabled', 'true');
    updateEditTotal();
});

// Remove product in edit modal
$(document).on('click', '.remove-product-edit', function () {
    var productId = $(this).data('id');
    $('#edit-product-purchases-' + productId).remove();
    $('.add-product-edit-btn[data-id="' + productId + '"]').removeClass('disabled').removeAttr(
        'aria-disabled');
    updateEditTotal();
});

// Update total in edit modal
$(document).on('input', '.quantity-input, .unit-price-input', function () {
    updateEditTotal();
});

function updateEditTotal() {
    var total = 0;
    $('#edit-order-list .product-item').each(function () {
        var quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
        var unitPrice = parseFloat($(this).find('.unit-price-input').val()) || 0;
        total += quantity * unitPrice;
    });
    $('#edit-total-price').text(total.toFixed(2));
}
// Global variable to store all products
var allModalProducts = [];

// Add Full Modal

$('#addFullModal').on('show.bs.modal', function () {
    // Clear search input
    $('#modal-search-input').val('');

    // Load products table if not already loaded
    if (allModalProducts.length === 0) {
        loadAllModalProducts();
    } else {
        displayModalProducts(allModalProducts);
    }

    // Clear cart
    $('#add-full-order-list').html('');
    $('#add-total-price').text('0.00');
    $('#add-supplier-id').val('');
});

// Function to load all products in modal
function loadAllModalProducts() {
    $.get('/admin/items-data', function (items) {
        allModalProducts = items;
        displayModalProducts(items);
    }).fail(function (xhr, status, error) {
        console.error('Failed to load items:', status, error);
        $('#add-products-table').html(
            '<tr><td colspan="4">فشل في تحميل المنتجات</td></tr>');
    });
}

// Function to display products in modal table
function displayModalProducts(items) {
    if (items.length === 0) {
        $('#add-products-table').html(
            '<tr><td colspan="4">لا توجد منتجات متاحة</td></tr>');
        return;
    }
    var tableHtml = '';
    items.forEach(function (item, index) {
        tableHtml += '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td>' + item.name + '</td>' +
            '<td>' + item.stock + '</td>' +
            '<td>' + item.price + '</td>' +
            '<td><a class="btn btn-dark btn-sm add-full-product-btn" data-id="' +
            item.id + '" data-name="' + item.name + '" data-price="' + item
                .price + '" href="#">إضافة</a></td>' +
            '</tr>';
    });
    $('#add-products-table').html(tableHtml);
}


// Add product in add modal
$(document).on('click', '.add-full-product-btn', function (e) {
    e.preventDefault();
    var $btn = $(this);
    if ($btn.hasClass('disabled')) return;

    var productId = $btn.data('id');
    var productName = $btn.data('name');
    var productPrice = $btn.data('price');

    if ($('#edit-product-' + productId).length > 0) {
        alert('المنتج موجود بالفعل');
        return;
    }

    var productHtml = '<div class="row mb-2 product-item" id="full-product-' + productId +
        '">' +
        '<div class="col-3"><strong>' + productName + '</strong></div>' +
        '<div class="col-3"><input type="number" class="form-control quantity-input" name="products[' +
        productId + '][quantity]" value="1" min="1" required></div>' +
        '<div class="col-4"><input type="number" step="0.01" class="form-control unit-price-input" name="products[' +
        productId + '][unit_price]"   min="0" required></div>' +
        '<div class="col-1"><button type="button" class="btn btn-danger btn-sm remove-full-product" data-id="' +
        productId + '">حذف</button></div>' +
        '</div>';

    $('#add-full-order-list').append(productHtml);
    $btn.addClass('disabled').attr('aria-disabled', 'true');
    updateAddTotal();
});

$(document).on('click', '.remove-full-product', function () {
    var productId = $(this).data('id');
    $('#full-product-' + productId).remove();
    $('.add-full-product-btn[data-id="' + productId + '"]').removeClass('disabled').removeAttr(
        'aria-disabled');
    updateAddTotal();
});
// Update total in add modal
$(document).on('input', '#addFullModal .quantity-input, #addFullModal .unit-price-input', function () {
    updateAddTotal();
});

function updateAddTotal() {
    var total = 0;
    $('#add-full-order-list .product-item').each(function () {
        var quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
        var unitPrice = parseFloat($(this).find('.unit-price-input').val()) || 0;
        total += quantity * unitPrice;
    });
    $('#add-total-price').text(total.toFixed(4));
}
            });
