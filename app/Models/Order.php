<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'order_number',
        'quantity',
        'unit_price',
        'original_price',
        'discount_amount',
        'was_on_sale',
        'total_amount',
        'status',
        'shipping_address',
        'phone_number',
        'notes',
        'payment_verified_at',
        'verified_by',
        'admin_notes',
        'payment_method',
        'full_name',
        'email',
        'city',
        'postal_code',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'was_on_sale' => 'boolean',
            'payment_verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('payment_screenshot')->singleFile();
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $attempts = 0;
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(microtime()), 0, 6));

            // If orders table doesn't have order_number column (older DBs), skip exists() check
            if (!\Illuminate\Support\Facades\Schema::hasColumn('orders', 'order_number')) {
                break;
            }

            $exists = self::where('order_number', $orderNumber)->exists();
            $attempts++;
        } while ($exists && $attempts < 10);

        // if somehow still exists after attempts, append a random suffix
        if (isset($exists) && $exists) {
            $orderNumber .= '-' . strtoupper(\Illuminate\Support\Str::random(4));
        }

        return $orderNumber;
    }

    /**
     * Get status badge color for Filament
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => 'gray',
            'payment_verification' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'purple',
            'shipped' => 'orange',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get total savings for this order
     */
    public function getTotalSavings(): float
    {
        return $this->was_on_sale ? ($this->discount_amount * $this->quantity) : 0;
    }

    /**
     * Get the percentage discount for this order
     */
    public function getDiscountPercentage(): int
    {
        if (!$this->was_on_sale || !$this->original_price || $this->original_price <= 0) {
            return 0;
        }
        
        return (int) round(($this->discount_amount / $this->original_price) * 100);
    }

    /**
     * Get the original total amount (before discount)
     */
    public function getOriginalTotalAmount(): float
    {
        return $this->original_price ? ($this->original_price * $this->quantity) : $this->total_amount;
    }
}
