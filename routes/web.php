<?php

use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Route;

 

// FacadesAuth::routes(['register'=> false]);
FacadesAuth::routes();
// Admin resource routes (protected)
Route::middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    route::group(['prefix' => 'admin'], function () {
        Route::resource('customers', App\Http\Controllers\CustomerController::class);
        Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
        Route::resource('stock', App\Http\Controllers\StockController::class);
        Route::post('stock/dispose', [App\Http\Controllers\StockController::class, 'dispose'])->name('stock.dispose');
        Route::resource('items', App\Http\Controllers\ItemController::class);
        Route::get('items-data', [App\Http\Controllers\ItemController::class, 'getItemsData'])->name('items.data');
        Route::get('items/{item}/stock', [App\Http\Controllers\ItemController::class, 'getStock'])->name('items.stock');
        // Sales and lines
        Route::resource('sales', App\Http\Controllers\SaleController::class);
        Route::put('sales/{sale}/update-sales', [App\Http\Controllers\SaleController::class, 'updateSales'])->name('sales.update-sales');
        Route::get('sales/{sale}/data', [App\Http\Controllers\SaleController::class, 'getSaleData'])->name('sales.data');
        Route::post('sales/{sale}/confirm', [App\Http\Controllers\SaleController::class, 'confirm'])->name('sales.confirm');
        Route::post('sale-lines', [App\Http\Controllers\SaleLineController::class, 'store'])->name('sale-lines.store');
        Route::get('sales/{id}/show-sales-order', [SaleController::class, 'print_salas_order'])->name('show-sales-order');
        Route::get('sales/{id}/getSaleData', [SaleController::class, 'getSaleData'])->name('getSaleData');
        Route::delete('sale-lines/{saleLine}', [App\Http\Controllers\SaleLineController::class, 'destroy'])->name('sale-lines.destroy');
        // Purchases and lines
        Route::resource('purchases', App\Http\Controllers\PurchaseController::class);
        Route::get('purchases/{purchase}/data', [App\Http\Controllers\PurchaseController::class, 'getPurchaseData'])->name('purchases.data');
        Route::get('create-full', [App\Http\Controllers\PurchaseController::class, 'createFull'])->name('purchases.create-full');
        Route::get('purchases/{purchase}/edit-full', [App\Http\Controllers\PurchaseController::class, 'editFull'])->name('purchases.edit-full');
        Route::put('purchases/{purchase}/update-full', [App\Http\Controllers\PurchaseLineController::class, 'updateFull'])->name('purchases.update-full');
        Route::post('purchases/store-full', [App\Http\Controllers\PurchaseLineController::class, 'storeFull'])->name('purchases.store-full');
        Route::post('purchases/{purchase}/confirm', [App\Http\Controllers\PurchaseController::class, 'confirm'])->name('purchases.confirm');
        Route::post('purchase-lines', [App\Http\Controllers\PurchaseLineController::class, 'store'])->name('purchase-lines.store');
        Route::delete('purchase-lines/{purchaseLine}', [App\Http\Controllers\PurchaseLineController::class, 'destroy'])->name('purchase-lines.destroy');
        Route::resource('accounts', App\Http\Controllers\AccountController::class);
        Route::get('debts', [App\Http\Controllers\AccountController::class, 'debts'])->name('accounts.debts');
        // transactions and lines
        Route::resource('transactions', App\Http\Controllers\TransactionController::class);
        Route::get('transactions/receipt/create', [App\Http\Controllers\TransactionController::class, 'createReceipt'])->name('transactions.receipt.create');
        Route::post('transactions/receipt', [App\Http\Controllers\TransactionController::class, 'storeReceipt'])->name('transactions.receipt.store');
        Route::get('transactions/payment/create', [App\Http\Controllers\TransactionController::class, 'createPayment'])->name('transactions.payment.create');
        Route::post('transactions/payment', [App\Http\Controllers\TransactionController::class, 'storePayment'])->name('transactions.payment.store');
        Route::resource('employees', App\Http\Controllers\EmployeeController::class);
        Route::resource('payroll-transactions', App\Http\Controllers\PayrollTransactionController::class);
        Route::resource('expenses', App\Http\Controllers\ExpenseController::class);
        Route::resource('invoices', App\Http\Controllers\InvoiceController::class);
        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::resource('roles', App\Http\Controllers\RoleController::class);
        Route::resource('permissions', App\Http\Controllers\PermissionsController::class);
    });
});
