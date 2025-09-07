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
            $table->string('payment_method')->after('notes'); // cod, qr_payment
            $table->string('full_name')->after('payment_method');
            $table->string('email')->after('full_name');
            $table->string('city', 100)->after('email');
            $table->string('postal_code', 10)->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'full_name', 'email', 'city', 'postal_code']);
        });
    }
};
