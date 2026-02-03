<?php

namespace App\Providers;

use Illuminate\pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Stock;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $stocks = Stock::whereNotNull('expiry')
            ->where('status', '!=', 'dispose')
            ->where('quantity', '>', 0)
            ->whereDate("expiry", "<=", now()->toDateString())
            ->update(['is_expired' => 1]);

        Paginator::useBootstrapFive();
        // Share notification data with all views
        View::composer('*', function ($view) {
            $expiredStocks = Stock::with('item')
                ->where('status', '!=', 'dispose')
                ->where('is_expired', true)
                ->whereNotNull('expiry')
                ->whereDate("expiry", "<=", now()->toDateString())
                ->where('quantity', '>', 0)
                ->get();

            // Products expiring within 7 days
            $expiringSoonStocks = Stock::with('item')
                // ->where('is_expired', false)
                ->whereNotNull('expiry')
                ->whereDate('expiry', '>=', now()->toDateString())
                ->whereDate('expiry', '<=', now()->addDays(7)->toDateString())
                ->where('quantity', '>', 0)
                ->get();

            $expiredStocksCount = $expiredStocks->count();
            $expiringSoonCount = $expiringSoonStocks->count();
            $totalNotificationsCount = $expiredStocksCount + $expiringSoonCount;

            $view->with('expiredStocks', $expiredStocks);
            $view->with('expiringSoonStocks', $expiringSoonStocks);
            $view->with('expiredStocksCount', $expiredStocksCount);
            $view->with('expiringSoonCount', $expiringSoonCount);
            $view->with('totalNotificationsCount', $totalNotificationsCount);
        });
    }
}
