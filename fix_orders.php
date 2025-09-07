<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Order;

echo "=== FIXING EXISTING ORDERS ===\n";

$orders = Order::with('product')->whereNull('original_price')->get();

foreach ($orders as $order) {
    $product = $order->product;
    
    echo "Fixing Order: {$order->order_number}\n";
    echo "Product: {$product->name}\n";
    echo "Current unit_price: {$order->unit_price}\n";
    
    // Calculate correct prices
    $currentPrice = $product->getCurrentPrice();
    $originalPrice = $product->price;
    $discountAmount = $product->getSavings();
    $wasOnSale = $product->is_on_sale && $product->sale_price !== null;
    
    echo "Should be:\n";
    echo "- Current Price: {$currentPrice}\n";
    echo "- Original Price: {$originalPrice}\n";
    echo "- Discount: {$discountAmount}\n";
    echo "- Was on Sale: " . ($wasOnSale ? 'Yes' : 'No') . "\n";
    
    // Update the order
    $order->update([
        'unit_price' => $currentPrice,
        'original_price' => $originalPrice,
        'discount_amount' => $discountAmount,
        'was_on_sale' => $wasOnSale,
        'total_amount' => $currentPrice * $order->quantity,
    ]);
    
    echo "Order updated!\n";
    echo "---\n";
}

echo "All orders have been fixed!\n";
