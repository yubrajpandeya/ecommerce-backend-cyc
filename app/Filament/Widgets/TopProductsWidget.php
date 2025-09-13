<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Throwable;

class TopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Selling Products';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Guard against missing DB or tables
                (function () {
                    try {
                        if (! Schema::hasTable('products')) {
                            return Product::query()->whereRaw('1 = 0');
                        }

                        return Product::query()
                            ->withCount(['orders' => function (Builder $query) {
                                $query->whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year);
                            }])
                            ->withSum(['orders' => function (Builder $query) {
                                $query->whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year);
                            }], 'total_amount')
                            ->where('is_active', true)
                            ->orderBy('orders_count', 'desc')
                            ->limit(10);
                    } catch (Throwable $e) {
                        Log::error('TopProductsWidget: DB unavailable: ' . $e->getMessage());
                        return Product::query()->whereRaw('1 = 0');
                    }
                })()
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->getStateUsing(fn (?Product $record): string => $record ? $record->getFirstMediaUrl('image') : '')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Orders')
                    ->badge()
                    ->color('success')
                    ->suffix(' sold'),
                Tables\Columns\TextColumn::make('orders_sum_total_amount')
                    ->label('Revenue')
                    ->formatStateUsing(fn ($state) => 'Rs. ' . number_format((float) $state, 2))
                    ->color('primary')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 5 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => 'Rs. ' . number_format((float) $state, 2))
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (?Product $record): string => $record ? route('filament.admin.resources.products.view', $record) : '#')
                    ->icon('heroicon-m-eye')
                    ->visible(fn (?Product $record): bool => $record !== null),
            ])
            ->paginated(false);
    }
}
