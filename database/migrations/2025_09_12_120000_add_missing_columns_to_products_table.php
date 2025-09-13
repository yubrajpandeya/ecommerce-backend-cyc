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
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('products', 'is_on_sale')) {
                $table->boolean('is_on_sale')->default(false);
            }
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
            if (!Schema::hasColumn('products', 'is_upcoming')) {
                $table->boolean('is_upcoming')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_upcoming')) {
                $table->dropColumn('is_upcoming');
            }
            if (Schema::hasColumn('products', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
            if (Schema::hasColumn('products', 'is_on_sale')) {
                $table->dropColumn('is_on_sale');
            }
            if (Schema::hasColumn('products', 'sale_price')) {
                $table->dropColumn('sale_price');
            }
            if (Schema::hasColumn('products', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropColumn('category_id');
            }
        });
    }
};
