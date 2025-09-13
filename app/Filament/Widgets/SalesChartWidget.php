<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Throwable;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Sales Overview (Last 30 Days)';
    
    protected static ?int $sort = 2;
    
    protected static string $color = 'info';
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = $this->getSalesData();
        
        return [
            'datasets' => [
                [
                    'label' => 'Sales (Rs.)',
                    'data' => $data['sales'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
                    'fill' => true,
                ],
                [
                    'label' => 'Orders',
                    'data' => $data['orders'],
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'pointBackgroundColor' => 'rgb(16, 185, 129)',
                    'fill' => true,
                    'yAxisID' => 'y1',
                ]
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Sales (Rs.)'
                    ]
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Orders'
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }

    private function getSalesData(): array
    {
        $labels = [];
        $salesData = [];
        $ordersData = [];

        try {
            if (! Schema::hasTable('orders')) {
                // return zeroed data
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('M j');
                    $salesData[] = 0.0;
                    $ordersData[] = 0;
                }
            } else {
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('M j');
                    
                    $dailySales = Order::whereDate('created_at', $date)->sum('total_amount');
                    $dailyOrders = Order::whereDate('created_at', $date)->count();
                    
                    $salesData[] = (float) $dailySales;
                    $ordersData[] = $dailyOrders;
                }
            }
        } catch (Throwable $e) {
            Log::error('SalesChartWidget: DB unavailable or query failed: ' . $e->getMessage());
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('M j');
                $salesData[] = 0.0;
                $ordersData[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'sales' => $salesData,
            'orders' => $ordersData,
        ];
    }
}
