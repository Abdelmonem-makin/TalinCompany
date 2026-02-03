<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$item = \App\Models\Item::first();
if ($item) {
    echo 'Item found: ' . $item->name . PHP_EOL;
    $stock = \App\Models\Stock::create([
        'item_id' => $item->id,
        'change' => 10,
        'qun' => 10,
        'type' => 'purchase',
        'expiry' => now()->subDays(1)->toDateString(),
        'status' => 'confirm',
        'is_expired' => false
    ]);
    $item->increment('stock', 10);
    echo 'Created expired stock for item: ' . $item->name . PHP_EOL;
    echo 'Expiry: ' . $stock->expiry . PHP_EOL;
} else {
    echo 'No items found' . PHP_EOL;
}
