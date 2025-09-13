<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Schema;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationGroup = 'Catalog';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        // Prepare the basic information fields separately so we can return an
        // array of Component instances and merge them into the section schema.
        $basicInformationFields = (function () {
            $name = Forms\Components\TextInput::make('name')
                ->required()
                ->live(onBlur: true);

            if (Schema::hasColumn('products', 'slug')) {
                $name = $name->afterStateUpdated(fn (string $context, $state, Forms\Set $set) => $context === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null);

                $slug = Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(Product::class, 'slug', ignoreRecord: true)
                    ->helperText('URL-friendly version of the name');

                return [$name, $slug];
            }

            return [$name];
        })();

        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema(array_merge($basicInformationFields, [
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('slug')->required(),
                                Forms\Components\Textarea::make('description'),
                                Forms\Components\Toggle::make('is_active')->default(true),
                            ]),
                        // Include description only if the column exists in the DB
                        ...(Schema::hasColumn('products', 'description') ? [
                            Forms\Components\RichEditor::make('description')
                                ->columnSpanFull()
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'underline',
                                    'bulletList',
                                    'orderedList',
                                    'link',
                                ]),
                        ] : []),
                    ]))
                    ->columns(2),
                    
                Forms\Components\Section::make('Pricing & Inventory')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rs.')
                            ->minValue(0),
                        Forms\Components\TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Current stock quantity'),
                        Forms\Components\Toggle::make('is_on_sale')
                            ->label('On Sale')
                            ->helperText('Enable to set a sale price for this product')
                            ->live()
                            ->default(false),
                        // Only show sale price and is_on_sale controls if DB columns exist
                        ...(Schema::hasColumn('products', 'is_on_sale') ? [
                            Forms\Components\Toggle::make('is_on_sale')
                                ->label('On Sale')
                                ->helperText('Enable to set a sale price for this product')
                                ->live()
                                ->default(false),
                        ] : []),
                        ...(Schema::hasColumn('products', 'sale_price') ? [
                            Forms\Components\TextInput::make('sale_price')
                                ->label('Sale Price')
                                ->numeric()
                                ->prefix('Rs.')
                                ->visible(fn (Forms\Get $get): bool => $get('is_on_sale'))
                                ->required(fn (Forms\Get $get): bool => $get('is_on_sale'))
                                ->minValue(0)
                                ->rules([
                                    fn (Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $regularPrice = $get('price');
                                        if ($get('is_on_sale') && $value && $regularPrice && (float) $value >= (float) $regularPrice) {
                                            $fail('Sale price must be less than the regular price.');
                                        }
                                    },
                                ])
                                ->helperText('Sale price must be less than regular price'),
                        ] : []),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Status & Availability')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Product will be visible to customers')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured')
                            ->helperText('Mark as featured to show on homepage')
                            ->default(false),
                        Forms\Components\Toggle::make('is_upcoming')
                            ->label('Upcoming Product')
                            ->live()
                            ->default(false),
                        Forms\Components\DatePicker::make('available_from')
                            ->label('Available From')
                            ->visible(fn (Forms\Get $get): bool => $get('is_upcoming'))
                            ->required(fn (Forms\Get $get): bool => $get('is_upcoming'))
                            ->native(false),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Product Images')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->collection('image')
                            ->disk('public')
                            ->directory('products')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('800')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText('Upload product image (recommended: 800x800px)'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                // Show slug column only when present in the DB schema
                ...(Schema::hasColumn('products', 'slug') ? [
                    Tables\Columns\TextColumn::make('slug')
                        ->searchable(),
                ] : []),
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => 'Rs. ' . number_format((float) $state, 2))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->formatStateUsing(fn ($state) => $state ? 'Rs. ' . number_format((float) $state, 2) : '-')
                    ->label('Sale Price')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_price')
                    ->label('Current Price')
                    ->getStateUsing(fn (?Product $record): string => $record ? 'Rs. ' . number_format($record->getCurrentPrice(), 2) : '-')
                    ->badge()
                    ->color(fn (?Product $record): string => $record && $record->is_on_sale ? 'success' : 'gray'),
                Tables\Columns\IconColumn::make('is_on_sale')
                    ->label('On Sale')
                    ->boolean()
                    ->trueIcon('heroicon-o-fire')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('danger')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('discount_percentage')
                    ->label('Discount')
                    ->getStateUsing(fn (?Product $record): string => $record && $record->is_on_sale ? $record->getDiscountPercentage() . '%' : '-')
                    ->badge()
                    ->color('warning')
                    ->visible(fn (?Product $record): bool => $record ? $record->is_on_sale : false),
                // Stock column may be missing on some DBs; show only if present
                ...(Schema::hasColumn('products', 'stock') ? [
                    Tables\Columns\TextColumn::make('stock')
                        ->numeric()
                        ->sortable(),
                ] : []),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_upcoming')
                    ->boolean(),
                Tables\Columns\TextColumn::make('available_from')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active products')
                    ->falseLabel('Inactive products')
                    ->native(false),
                    
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueLabel('Featured products')
                    ->falseLabel('Non-featured products')
                    ->native(false),
                    
                Tables\Filters\TernaryFilter::make('is_on_sale')
                    ->label('On Sale')
                    ->boolean()
                    ->trueLabel('Products on sale')
                    ->falseLabel('Regular price products')
                    ->native(false),
                    
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock (≤10)')
                    ->query(fn (Builder $query): Builder => $query->where('stock', '<=', 10))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('out_of_stock')
                    ->label('Out of Stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock', 0))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('quick_stock_update')
                    ->label('Update Stock')
                    ->icon('heroicon-m-squares-plus')
                    ->form([
                        Forms\Components\TextInput::make('new_stock')
                            ->label('New Stock Quantity')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $record->update(['stock' => $data['new_stock']]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Stock updated successfully')
                            ->body("Stock for {$record->name} updated to {$data['new_stock']}")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_active' => true]))
                        ->color('success'),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_active' => false]))
                        ->color('danger'),
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Mark as Featured')
                        ->icon('heroicon-o-star')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_featured' => true]))
                        ->color('warning'),
                    Tables\Actions\BulkAction::make('unfeature')
                        ->label('Remove from Featured')
                        ->icon('heroicon-o-star')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_featured' => false]))
                        ->color('gray'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
