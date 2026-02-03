<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$item = \App\Models\Item::first();
echo 'Item: ' . $item->name . ' Stock: ' . $item->stock . PHP_EOL;

$sale = \App\Models\Sales::create([
    'customer_id' => 1, // assuming customer exists
    'total' => 0,
    'status' => 'pending'
]);

echo 'Created sale ID: ' . $sale->id . PHP_EOL;

// Try to add sale line for 10 units (should fail)
$request = new \Illuminate\Http\Request();
$request->merge([
    'sale_id' => $sale->id,
    'item_id' => $item->id,
    'quantity' => 10,
    'unit_price' => 10
]);

$controller = new \App\Http\Controllers\SaleLineController();
try {
    $response = $controller->store($request);
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo 'Response is redirect' . PHP_EOL;
        // Check if sale line was created
        $saleLines = \App\Models\SaleLine::where('sale_id', $sale->id)->get();
        if ($saleLines->count() > 0) {
            echo 'Sale line created - validation failed' . PHP_EOL;
        } else {
            echo 'Sale line not created - validation worked' . PHP_EOL;
        }
    } else {
        echo 'Unexpected response type' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Exception: ' . $e->getMessage() . PHP_EOL;
}
