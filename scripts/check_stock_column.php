<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

if (! Schema::hasTable('products')) {
    echo "NO_PRODUCTS_TABLE\n";
    exit(0);
}

if (! Schema::hasColumn('products', 'stock')) {
    echo "NO_STOCK_COLUMN\n";
    exit(0);
}
$count = DB::table('products')->where('stock', '<=', 5)->where('is_active', 1)->count();
echo "low_stock_count: " . $count . "\n";
