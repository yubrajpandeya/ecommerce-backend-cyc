<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
        'is_featured',
        'is_upcoming',
        'available_from',
        'is_on_sale',
        'sale_price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_upcoming' => 'boolean',
            'is_on_sale' => 'boolean',
            'available_from' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the current selling price (sale price if on sale, otherwise regular price)
     */
    public function getCurrentPrice(): float
    {
        return $this->is_on_sale && $this->sale_price ? $this->sale_price : $this->price;
    }

    /**
     * Get the savings amount if product is on sale
     */
    public function getSavings(): float
    {
        return $this->is_on_sale && $this->sale_price ? $this->price - $this->sale_price : 0;
    }

    /**
     * Get the discount percentage if product is on sale
     */
    public function getDiscountPercentage(): int
    {
        if (!$this->is_on_sale || !$this->sale_price || $this->price <= 0) {
            return 0;
        }
        
        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
}
