<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add New Product')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            // ProductResource\Widgets\ProductStatsWidget::class,
        ];
    }
}
