<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class FixOrderPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:fix-pricing {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix order pricing to apply correct discounts and track savings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }
        
        $this->info('📋 Looking for orders with incorrect pricing...');
        
        // Find orders that might have incorrect pricing
        $orders = Order::with('product')
            ->where(function ($query) {
                $query->whereNull('original_price')
                    ->orWhere('discount_amount', 0)
                    ->orWhere('was_on_sale', false);
            })
            ->get();
            
        if ($orders->isEmpty()) {
            $this->info('✅ All orders have correct pricing!');
            return;
        }
        
        $this->info("🔧 Found {$orders->count()} orders to fix:");
        
        $fixedCount = 0;
        
        foreach ($orders as $order) {
            $product = $order->product;
            
            if (!$product) {
                $this->warn("⚠️  Order {$order->order_number} has no product - skipping");
                continue;
            }
            
            // Calculate correct prices
            $currentPrice = $product->getCurrentPrice();
            $originalPrice = $product->price;
            $discountAmount = $product->getSavings();
            $wasOnSale = $product->is_on_sale && $product->sale_price !== null;
            
            $this->line("📦 Order: {$order->order_number}");
            $this->line("   Product: {$product->name}");
            $this->line("   Current: {$order->unit_price} → {$currentPrice}");
            $this->line("   Discount: {$order->discount_amount} → {$discountAmount}");
            $this->line("   Total: {$order->total_amount} → " . ($currentPrice * $order->quantity));
            
            if (!$dryRun) {
                // Update the order
                $order->update([
                    'unit_price' => $currentPrice,
                    'original_price' => $originalPrice,
                    'discount_amount' => $discountAmount,
                    'was_on_sale' => $wasOnSale,
                    'total_amount' => $currentPrice * $order->quantity,
                ]);
                $this->info("   ✅ Fixed!");
            } else {
                $this->comment("   📝 Would be fixed");
            }
            
            $fixedCount++;
        }
        
        if (!$dryRun) {
            $this->info("🎉 Successfully fixed {$fixedCount} orders!");
        } else {
            $this->info("📊 Found {$fixedCount} orders that need fixing. Run without --dry-run to apply changes.");
        }
    }
}
