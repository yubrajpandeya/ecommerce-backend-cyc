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
        Schema::table('products', function (Blueprint $table) {
            // Add columns only if they're missing to avoid errors on older schemas
            if (! Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }

            if (! Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('description');
            }

            if (! Schema::hasColumn('products', 'stock')) {
                $table->unsignedInteger('stock')->default(0)->after('price');
            }

            if (! Schema::hasColumn('products', 'is_on_sale')) {
                $table->boolean('is_on_sale')->default(false)->after('stock');
            }

            if (! Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->after('is_on_sale');
            }

            if (! Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sale_price')) {
                $table->dropColumn('sale_price');
            }
            if (Schema::hasColumn('products', 'is_on_sale')) {
                $table->dropColumn('is_on_sale');
            }
            if (Schema::hasColumn('products', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
            // Keep description/price/stock if they existed previously; only drop if we added them.
            // For safety, we will not drop description/price/stock here to avoid accidental data loss.
        });
    }
};
