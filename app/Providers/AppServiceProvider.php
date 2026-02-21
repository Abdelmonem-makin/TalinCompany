<?php

namespace App\Providers;

use Illuminate\pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
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
        if (Schema::hasTable('stocks')) {
            $toExpire = Stock::whereNotNull('expiry')
                ->where('status', '!=', 'dispose')
                ->where('quantity', '>', 0)
                ->whereDate('expiry', '<=', now()->toDateString())
                ->get();

            foreach ($toExpire as $stock) {
                if ($stock->is_expired) continue;
                $stock->is_expired = true;
                $stock->save();

                $remaining = $stock->remaining ?? 0;
                if ($remaining > 0) {
                    // create disposal record
                    Stock::create([
                        'item_id' => $stock->item_id,
                        'quantity' => -1 * $remaining,
                        'type' => 'تخلص',
                        'note' => 'تم التخلص من منتجات منتهية الصلاحية - كمية: ' . $remaining . ' من ' . ($stock->item->name ?? ''),
                        'status' => 'dispose',
                        'expiry' => $stock->expiry,
                    ]);

                    if ($stock->item) {
                        $stock->item->decrement('stock', $remaining);
                    }

                    $stock->remaining = 0;
                    $stock->status = 'dispose';
                    $stock->save();
                }
            }
        }

        Paginator::useBootstrapFive();
        // Share notification data with all views
        View::composer('*', function ($view) {
            $expiredStocks = Stock::with('item')
                ->where('status', '!=', 'dispose')
                ->where('is_expired', true)
                ->whereNotNull('expiry')
                ->whereDate('expiry', '<=', now()->toDateString())
                ->where('quantity', '>', 0)
                ->get();
            //    dd( $expiredStocks->is_expired =  1 );
            // Products expiring within 7 days
            $expiringSoonStocks = Stock::with('item')
                // ->where('is_expired', true)
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
