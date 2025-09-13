<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'order_number')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_number')->unique()->after('id');
            });

            // Generate order numbers for existing rows
            try {
                $orders = app()->make(\Illuminate\Database\Eloquent\Model::class);
            } catch (\Throwable $e) {
                // fallback - we'll use DB facade directly
            }

            $rows = \Illuminate\Support\Facades\DB::table('orders')->select('id')->get();
            foreach ($rows as $row) {
                $generated = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(microtime() . $row->id), 0, 6));
                // ensure uniqueness
                $exists = \Illuminate\Support\Facades\DB::table('orders')->where('order_number', $generated)->exists();
                if ($exists) {
                    $generated = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
                }
                \Illuminate\Support\Facades\DB::table('orders')->where('id', $row->id)->update(['order_number' => $generated]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'order_number')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('order_number');
            });
        }
    }
};
