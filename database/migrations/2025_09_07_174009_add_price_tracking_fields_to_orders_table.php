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
            $table->decimal('original_price', 10, 2)->after('unit_price')->nullable(); // Original product price
            $table->decimal('discount_amount', 10, 2)->after('original_price')->default(0); // Discount amount
            $table->boolean('was_on_sale')->after('discount_amount')->default(false); // Track if bought on sale
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'discount_amount', 'was_on_sale']);
        });
    }
};
