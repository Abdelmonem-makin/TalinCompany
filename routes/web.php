<?php

use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// FacadesAuth::routes(['register'=> false]);
FacadesAuth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin resource routes (protected)
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
    Route::resource('stock', App\Http\Controllers\StockController::class);
    Route::resource('items', App\Http\Controllers\ItemController::class);
    // Sales and lines
    Route::resource('sales', App\Http\Controllers\SaleController::class);
    Route::post('sales/{sale}/confirm', [App\Http\Controllers\SaleController::class, 'confirm'])->name('sales.confirm');
    Route::post('sale-lines', [App\Http\Controllers\SaleLineController::class, 'store'])->name('sale-lines.store');
    Route::get('sales/{id}/show-sales-order', [SaleController::class, 'show_sales_order'])->name('show-sales-order');
    Route::delete('sale-lines/{saleLine}', [App\Http\Controllers\SaleLineController::class, 'destroy'])->name('sale-lines.destroy');
    // Purchases and lines
    Route::resource('purchases', App\Http\Controllers\PurchaseController::class);
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
});
