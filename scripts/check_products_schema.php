<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Default connection: " . config('database.default') . PHP_EOL;
echo "Database name: " . config('database.connections.' . config('database.default') . '.database') . PHP_EOL;

// Debug: show environment sources
echo "getenv('DB_CONNECTION'): " . var_export(getenv('DB_CONNECTION'), true) . PHP_EOL;
if (function_exists('env')) {
    echo "env('DB_CONNECTION'): " . var_export(env('DB_CONNECTION'), true) . PHP_EOL;
}
echo "_ENV['DB_CONNECTION']: " . var_export(isset($_ENV['DB_CONNECTION']) ? $_ENV['DB_CONNECTION'] : null, true) . PHP_EOL;
echo "getenv('DB_DATABASE'): " . var_export(getenv('DB_DATABASE'), true) . PHP_EOL;

$table = 'products';
if (!Schema::hasTable($table)) {
    echo "Table '$table' does not exist.\n";
    exit(1);
}

$columns = Schema::getColumnListing($table);
echo "Columns in '$table':\n";
foreach ($columns as $col) {
    echo " - $col\n";
}

// show a sample row
$row = DB::table($table)->first();
if ($row) {
    echo "\nSample row (first):\n";
    print_r($row);
} else {
    echo "\nNo rows in $table.\n";
}

return 0;
