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
        // Get today's sales
        $todaySales = \App\Models\Sales::whereDate('created_at', today())->sum('total');

        // Get total inventory
        $totalInventory = \App\Models\Stock::sum('quantity');

        // Get pending invoices
        $pendingInvoices = \App\Models\Invoice::where('status', 'pending')->count();

        // Get items running low
        $lowStockItems = \App\Models\Stock::where('quantity', '<=', 10)->count();

        // Get top customers
        $topCustomers = \App\Models\Customer::withSum('sales', 'total')
            ->orderBy('sales_sum_total_amount', 'desc')
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
