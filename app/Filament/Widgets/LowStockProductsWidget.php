<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Low Stock Alert';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('is_active', true)
                    ->where('stock', '<=', 10)
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 5 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => 'Rs. ' . number_format((float) $state, 2)),
            ])
            ->actions([
                Tables\Actions\Action::make('quick_restock')
                    ->label('Quick Restock')
                    ->icon('heroicon-m-plus')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('add_stock')
                            ->label('Add Stock')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $record->increment('stock', $data['add_stock']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Stock updated successfully')
                            ->body("Added {$data['add_stock']} units to {$record->name}")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
