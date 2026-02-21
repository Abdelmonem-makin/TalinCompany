<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get today's sales (Sales.total uses `total` column and `date` attribute)
        $todaySales = \App\Models\Sales::whereDate('date', today())->sum('total');

        // Get total inventory from Items table (accurate stock per item)
        $totalInventory = \App\Models\Item::sum('stock');

        // Get pending invoices (uses Invoice.status)
        $pendingInvoices = \App\Models\Stock::where('status', 'sold')->orwhere('status', 'draft')->count();

        // Get items running low (items with stock <= 10)
        $lowStockItems = \App\Models\Item::where('stock', '<=', 10)->count();

        // Get top customers by sales total
        $topCustomers = \App\Models\Customer::withSum('Sales', 'total')
            ->orderByDesc('Sales_sum_total')
            ->take(5)
            ->get();

        // Get recent transactions
        $recentTransactions = \App\Models\Transaction::latest()->take(5)->get();

        return view('home', compact(
            'todaySales',
            'totalInventory',
            'pendingInvoices',
            'lowStockItems',
            'topCustomers',
            'recentTransactions'
        ));
    }
}
