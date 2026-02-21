$(function () {
    // ==================== Show Product Modal Handler ====================
    // Handle loading content into modals via data-url attribute
    $('#show_modalId').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var url = button.data('url');
        
        if (url) {
            // Load content via AJAX
            $.get(url, function (data) {
                $('#order-list').html(data);
            }).fail(function (xhr, status, error) {
                console.error('Failed to load order:', status, error);
                $('#order-list').html('<div class="text-center p-4">حدث خطأ في تحميل البيانات</div>');
            });
        }
    });

    // ==================== User Edit Modal ====================
    // Handle click on edit user button
    $('.editUserBtn').on('click', function () {
        var userId = $(this).data('id');
        // Fetch user data via AJAX
        $.get('/admin/users/' + userId + '/data', function (data) {
            var user = data.user;
            var roles = data.roles;
            var permissions = data.permissions;

            // Set form action
            $('#editUserForm').attr('action', '/admin/users/' + userId);

            // Populate basic fields
            $('#edit-user-id').val(user.id);
            $('#edit-name').val(user.name);

            // Populate roles dropdown
            var rolesHtml = '<option disabled value="" selected>اختر الوظيفه</option>';
            var userRoleIds = user.roles.map(function (r) { return r.id; });
            roles.forEach(function (role) {
                var selected = userRoleIds.includes(role.id) ? 'selected' : '';
                rolesHtml += '<option value="' + role.id + '" ' + selected + '>' + role.display_name + '</option>';
            });
            $('#edit-roles').html(rolesHtml);

            // Group permissions by module
            var groupedPermissions = {};
            permissions.forEach(function (perm) {
                var parts = perm.name.split('_');
                var module = parts[0];
                var action = parts[1];
                if (!groupedPermissions[module]) {
                    groupedPermissions[module] = [];
                }
                groupedPermissions[module].push({
                    action: action,
                    id: perm.id,
                    name: perm.name
                });
            });

            // Get user's current permission IDs
            var userPermissionIds = user.permissions.map(function (p) { return p.id; });

            // Build tabs
            var tabsHtml = '';
            var contentHtml = '';
            var moduleKeys = Object.keys(groupedPermissions);
            moduleKeys.forEach(function (module, index) {
                var active = index === 0 ? 'active' : '';
                tabsHtml += '<li class="nav-item" role="presentation">' +
                    '<button class="nav-link ' + active + '" id="edit-tab-' + index + '" data-bs-toggle="tab" ' +
                    'data-bs-target="#edit-content-' + index + '" type="button" role="tab">' +
                    module + '</button></li>';

                contentHtml += '<div class="tab-pane ' + active + '" id="edit-content-' + index + '" role="tabpanel">' +
                    '<div class="row justify-content-sm-between">';

                groupedPermissions[module].forEach(function (perm) {
                    var checked = userPermissionIds.includes(perm.id) ? 'checked' : '';
                    contentHtml += '<div class="col-3 p-0">' +
                        '<div class="form-check p-0">' +
                        '<label style="font-size: 13px;" class="form-check-label">' +
                        '<input class="form-check-input" name="permissions[]" type="checkbox" ' + checked + ' ' +
                        'value="' + perm.id + '" />' +
                        perm.action + ' ' + module +
                        '</label></div></div>';
                });

                contentHtml += '</div></div>';
            });

            $('#editPermissionsTab').html(tabsHtml);
            $('#editPermissionsContent').html(contentHtml);

        })
            .fail(function (xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('حدث خطأ في جلب البيانات');
            })
            ;
    });

    // Handle edit user form submission
    // $('#editUserForm').on('submit', function (e) {
    //     e.preventDefault();
    //     var form = $(this);
    //     var url = form.attr('action');
    //     var formData = form.serialize();
        
    //     // Get CSRF token from meta tag
    //     var csrfToken = $('meta[name="csrf-token"]').attr('content');

    //     $.ajax({
    //         url: url,
    //         type: 'POST',
    //         data: formData,
    //         headers: {
    //             'X-CSRF-TOKEN': csrfToken
    //         },
    //         success: function (response) {
    //             $('#editUserModal').modal('hide');
    //             location.reload();
    //         },
    //         error: function (xhr, status, error) {
    //             console.error('AJAX error:', status, error);
    //             alert('حدث خطأ في تحديث البيانات');
    //         }
    //     });
    // });

    // ==================== User Create Modal ====================
    // Handle show create user modal - fetch roles and permissions
    // $('#createUserModal').on('show.bs.modal', function () {
    //     // Clear form fields
    //     $('#create-name').val('');
    //     $('#create-roles').html('<option disabled value="" selected>اختر الوظيفه</option>');
    //     $('#create-password').val('');
    //     $('#create-password-confirm').val('');

    //     // // Fetch roles and permissions via AJAX
    //     $.get('/admin/users/roles-permissions', function (data) {
    //         var roles = data.roles;
    //         var permissions = data.permissions;

    //         // Populate roles dropdown
    //         var rolesHtml = '<option disabled value="" selected>اختر الوظيفه</option>';
    //         roles.forEach(function (role) {
    //             rolesHtml += '<option value="' + role.id + '">' + role.display_name + '</option>';
    //         });
    //         $('#create-roles').html(rolesHtml);

    //         // Group permissions by module
    //         var groupedPermissions = {};
    //         permissions.forEach(function (perm) {
    //             var parts = perm.name.split('_');
    //             var module = parts[0];
    //             var action = parts[1];
    //             if (!groupedPermissions[module]) {
    //                 groupedPermissions[module] = [];
    //             }
    //             groupedPermissions[module].push({
    //                 action: action,
    //                 id: perm.id,
    //                 name: perm.name
    //             });
    //         });

    //         // Build tabs
    //         var tabsHtml = '';
    //         var contentHtml = '';
    //         var moduleKeys = Object.keys(groupedPermissions);
    //         moduleKeys.forEach(function (module, index) {
    //             var active = index === 0 ? 'active' : '';
    //             tabsHtml += '<li class="nav-item" role="presentation">' +
    //                 '<button class="nav-link ' + active + '" id="create-tab-' + index + '" data-bs-toggle="tab" ' +
    //                 'data-bs-target="#create-content-' + index + '" type="button" role="tab">' +
    //                 module + '</button></li>';

    //             contentHtml += '<div class="tab-pane ' + active + '" id="create-content-' + index + '" role="tabpanel">' +
    //                 '<div class="row justify-content-sm-between">';

    //             groupedPermissions[module].forEach(function (perm) {
    //                 contentHtml += '<div class="col-3 p-0">' +
    //                     '<div class="form-check p-0">' +
    //                     '<label style="font-size: 13px;" class="form-check-label">' +
    //                     '<input class="form-check-input" name="permissions[]" type="checkbox" ' +
    //                     'value="' + perm.id + '" />' +
    //                     perm.action + ' ' + module +
    //                     '</label></div></div>';
    //             });

    //             contentHtml += '</div></div>';
    //         });

    //         $('#createPermissionsTab').html(tabsHtml);
    //         $('#createPermissionsContent').html(contentHtml);

    //     })
    //         .fail(function (xhr, status, error) {
    //             console.error('AJAX error:', status, error);
    //             alert('حدث خطأ في جلب البيانات');
    //         });
    // });

    // Handle create user form submission
    $('#createUserForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var url = '/admin/users';
        var formData = form.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (response) {
                $('#createUserModal').modal('hide');
                location.reload();
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('حدث خطأ في إنشاء المستخدم');
            }
        });
    });

    // ==================== Existing Code ====================
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
                    '<td>' + line.item_type + '</td>' +
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
            loadCSS: '{{ asset("css/bootstrap.min.css") }}',
            pageTitle: "فاتورة مشتريات - " + $('#modal-reference-id').text(),
            removeInline: false,
            printDelay: 333,
            beforePrint: function () {
                $('body').css({
                    "direction": "rtl",
                    "text-aling": "right",

                });
            },
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
                    '<td>' + item.type + '</td>' +
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
                    '<td>' + item.type + '</td>' +
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
        $('#edit-product-' + productId).remove();
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
                '<td>' + item.type + '</td>' +
                '<td>' + item.stock + '</td>' +
                // '<td>' + item.price + '</td>' +
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
