<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Stock;
use App\Models\Item;

// Test the dispose functionality
echo "Testing dispose functionality...\n";

// Get expired stocks
$expiredStocks = Stock::where('is_expired', true)->where('qun', '>', 0)->get();
echo "Found " . $expiredStocks->count() . " expired stocks\n";

if ($expiredStocks->count() > 0) {
    $stock = $expiredStocks->first();
    echo "Testing disposal of stock ID: {$stock->id}, quantity: {$stock->qun}\n";

    // Simulate disposal request
    $disposals = [
        $stock->id => 5 // Dispose 5 units
    ];

    $disposedCount = 0;
    $totalDisposedQuantity = 0;

    foreach ($disposals as $stockId => $quantity) {
        $quantity = (int) $quantity;

        if ($quantity <= 0) {
            continue;
        }

        $stock = Stock::find($stockId);

        if (!$stock || !$stock->is_expired || $stock->qun < $quantity) {
            echo "Cannot dispose: stock not found, not expired, or insufficient quantity\n";
            continue;
        }

        echo "Before disposal: stock qun = {$stock->qun}, item stock = {$stock->item->stock}\n";

        // Create a disposal stock entry (negative quantity)
        Stock::create([
            'item_id' => $stock->item_id,
            'change' => -$quantity,
            'type' => 'disposal',
            'note' => 'تم التخلص من منتجات منتهية الصلاحية - كمية: ' . $quantity . ' من ' . $stock->item->name,
            'status' => 'confirm',
            'expiry' => $stock->expiry,
        ]);

        // Update the original stock entry
        $stock->decrement('qun', $quantity);

        // Update item stock
        $item = $stock->item;
        if ($item) {
            $item->decrement('stock', $quantity);
        }

        echo "After disposal: stock qun = {$stock->qun}, item stock = {$stock->item->stock}\n";

        $disposedCount++;
        $totalDisposedQuantity += $quantity;
    }

    echo "Disposal test completed: disposed {$totalDisposedQuantity} units from {$disposedCount} stocks\n";
} else {
    echo "No expired stocks found to test disposal\n";
}
