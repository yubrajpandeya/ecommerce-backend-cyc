<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Throwable;

class EcommerceStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Calculate stats, but guard against missing DB, drivers or unreachable DB
        $todayOrders = 0;
        $todayRevenue = 0;
        $monthlyRevenue = 0;
        $lowStockProducts = 0;
        $totalProducts = 0;
        $totalOrders = 0;

        try {
            if (Schema::hasTable('orders')) {
                $todayOrders = Order::whereDate('created_at', today())->count();
                $todayRevenue = Order::whereDate('created_at', today())->sum('total_amount');
                $monthlyRevenue = Order::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total_amount');
                $totalOrders = Order::count();
            }

            if (Schema::hasTable('products')) {
                // Only run these queries if products table exists
                $lowStockProducts = Product::where('stock', '<=', 5)->where('is_active', true)->count();
                $totalProducts = Product::where('is_active', true)->count();
            }
        } catch (Throwable $e) {
            // Log but don't bubble up so Filament/boot doesn't fail
            Log::error('EcommerceStatsWidget: database unavailable or query failed: ' . $e->getMessage());
            // keep defaults as zeros
        }

        return [
            Stat::make('Today\'s Orders', $todayOrders)
                ->description('Orders placed today')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),
                
            Stat::make('Today\'s Revenue', 'Rs. ' . number_format($todayRevenue, 2))
                ->description('Revenue generated today')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
                
            Stat::make('Monthly Revenue', 'Rs. ' . number_format($monthlyRevenue, 2))
                ->description('Revenue this month')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
                
            Stat::make('Low Stock Alert', $lowStockProducts)
                ->description($lowStockProducts > 0 ? 'Products with ≤5 items' : 'All products in stock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'warning' : 'success'),
                
            Stat::make('Active Products', $totalProducts)
                ->description('Total active products')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('gray'),
                
            Stat::make('Total Orders', $totalOrders)
                ->description('All time orders')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('gray'),
        ];
    }
}
