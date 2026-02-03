<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckExpiredStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired-stocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and mark expired stocks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredStocks = \App\Models\Stock::whereDate('expiry', '<=', now()->toDateString())
            ->where('is_expired', false)
            ->whereNotNull('expiry')
            ->get();

        foreach ($expiredStocks as $stock) {
            $stock->update(['is_expired' => true]);
            // Decrement the item's stock by the quantity in this stock entry
            if ($stock->item) {
                $stock->item->decrement('stock', $stock->qun);
            }
        }

        $this->info('Checked and marked ' . $expiredStocks->count() . ' expired stocks.');
    }
}
