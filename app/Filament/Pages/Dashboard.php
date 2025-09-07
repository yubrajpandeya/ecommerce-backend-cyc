<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $title = 'Dashboard';
    
    public function getColumns(): int | string | array
    {
        return 12;
    }
    
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\EcommerceStatsWidget::class,
            \App\Filament\Widgets\SalesChartWidget::class,
            \App\Filament\Widgets\LowStockProductsWidget::class,
            \App\Filament\Widgets\TopProductsWidget::class,
        ];
    }
}
