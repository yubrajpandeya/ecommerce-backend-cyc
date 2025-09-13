<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('orders', 'product_id')) {
                $table->foreignId('product_id')->after('user_id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('orders', 'quantity')) {
                $table->integer('quantity')->after('order_number')->default(1);
            }

            if (!Schema::hasColumn('orders', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->after('quantity')->default(0);
            }

            if (!Schema::hasColumn('orders', 'original_price')) {
                $table->decimal('original_price', 10, 2)->after('unit_price')->default(0);
            }

            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->after('original_price')->default(0);
            }

            if (!Schema::hasColumn('orders', 'was_on_sale')) {
                $table->boolean('was_on_sale')->after('discount_amount')->default(false);
            }

            if (!Schema::hasColumn('orders', 'status')) {
                $table->enum('status', ['pending', 'payment_verification', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending')->after('total_amount');
            }

            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->after('status');
            }

            if (!Schema::hasColumn('orders', 'phone_number')) {
                $table->string('phone_number')->after('shipping_address');
            }

            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('phone_number');
            }

            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('orders', 'full_name')) {
                $table->string('full_name')->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('orders', 'email')) {
                $table->string('email')->nullable()->after('full_name');
            }

            if (!Schema::hasColumn('orders', 'city')) {
                $table->string('city', 100)->nullable()->after('email');
            }

            if (!Schema::hasColumn('orders', 'postal_code')) {
                $table->string('postal_code', 10)->nullable()->after('city');
            }

            if (!Schema::hasColumn('orders', 'payment_verified_at')) {
                $table->timestamp('payment_verified_at')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('orders', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('payment_verified_at');
            }

            if (!Schema::hasColumn('orders', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('verified_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally empty to avoid accidental data loss on rollback.
    }
};
