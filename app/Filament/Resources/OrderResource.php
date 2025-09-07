<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationGroup = 'Sales';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Order Number')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Forms\Components\TextInput::make('unit_price')
                            ->label('Unit Price')
                            ->numeric()
                            ->prefix('Rs.')
                            ->required(),

                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('Rs.')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),

                Forms\Components\Section::make('Shipping Information')
                    ->schema([
                        Forms\Components\Textarea::make('shipping_address')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('phone_number')
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Order Status & Payment')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'payment_verification' => 'Payment Verification',
                                'confirmed' => 'Confirmed',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),

                        SpatieMediaLibraryFileUpload::make('payment_screenshot')
                            ->collection('payment_screenshot')
                            ->disk('public')
                            ->directory('payment-screenshots')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                            ->maxSize(2048),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('INR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'pending',
                        'yellow' => 'payment_verification',
                        'blue' => 'confirmed',
                        'purple' => 'processing',
                        'orange' => 'shipped',
                        'green' => 'delivered',
                        'red' => 'cancelled',
                    ])
                    ->sortable(),

                SpatieMediaLibraryImageColumn::make('payment_screenshot')
                    ->collection('payment_screenshot')
                    ->label('Payment Screenshot')
                    ->circular(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'payment_verification' => 'Payment Verification',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\Filter::make('created_today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today())),

                Tables\Filters\Filter::make('payment_pending')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'payment_verification')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('verify_payment')
                    ->label('Verify Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->status === 'payment_verification')
                    ->action(function (Order $record): void {
                        $record->update([
                            'status' => 'confirmed',
                            'payment_verified_at' => now(),
                            'verified_by' => auth()->id(),
                        ]);
                    }),
                Action::make('mark_shipped')
                    ->label('Mark as Shipped')
                    ->icon('heroicon-o-truck')
                    ->color('warning')
                    ->visible(fn (Order $record): bool => $record->status === 'confirmed' || $record->status === 'processing')
                    ->action(function (Order $record): void {
                        $record->update(['status' => 'shipped']);
                    }),
                Action::make('mark_delivered')
                    ->label('Mark as Delivered')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->status === 'shipped')
                    ->action(function (Order $record): void {
                        $record->update(['status' => 'delivered']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
