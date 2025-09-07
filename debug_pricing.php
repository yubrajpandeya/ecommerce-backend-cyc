<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Order;

echo "=== PRODUCTS ===\n";
$products = Product::all();
foreach ($products as $product) {
    echo "Product: {$product->name}\n";
    echo "Regular Price: {$product->price}\n";
    echo "Sale Price: {$product->sale_price}\n";
    echo "Is On Sale: " . ($product->is_on_sale ? 'Yes' : 'No') . "\n";
    echo "Current Price: {$product->getCurrentPrice()}\n";
    echo "Savings: {$product->getSavings()}\n";
    echo "---\n";
}

echo "\n=== RECENT ORDERS ===\n";
$orders = Order::with('product')->latest()->take(5)->get();
foreach ($orders as $order) {
    echo "Order: {$order->order_number}\n";
    echo "Product: {$order->product->name}\n";
    echo "Quantity: {$order->quantity}\n";
    echo "Unit Price (Final): {$order->unit_price}\n";
    echo "Original Price: {$order->original_price}\n";
    echo "Discount Amount: {$order->discount_amount}\n";
    echo "Total Amount: {$order->total_amount}\n";
    echo "Was on Sale: " . ($order->was_on_sale ? 'Yes' : 'No') . "\n";
    echo "---\n";
}
