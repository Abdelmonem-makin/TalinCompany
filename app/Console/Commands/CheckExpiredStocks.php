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
            // mark expired
            $stock->is_expired = true;
            $stock->save();

            // if there is remaining quantity, create a disposal entry and decrement item stock
            $remaining = $stock->remaining ?? 0;
            if ($remaining > 0) {
                \App\Models\Stock::create([
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

                // zero out the remaining on the original stock batch and mark disposed
                $stock->remaining = 0;
                $stock->status = 'dispose';
                $stock->save();
            }
        }

        $this->info('Checked and marked ' . $expiredStocks->count() . ' expired stocks.');
    }
}
