<?php
// Script: mark_existing_migrations.php
// Purpose: For migrations that create tables already present in DB, insert a record into `migrations` so Laravel won't attempt to recreate them.

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

echo "Scanning migration files...\n";
$files = glob(__DIR__ . '/../database/migrations/*.php');
$marked = 0;
foreach ($files as $file) {
    $filename = basename($file);
    $migrationName = pathinfo($filename, PATHINFO_FILENAME);

    // If already recorded, skip
    $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
    if ($exists) {
        echo "Already recorded: $migrationName\n";
        continue;
    }

    $contents = file_get_contents($file);

    // Try to infer table name from migration file name (create_xxx_table)
    if (preg_match('/create_([a-z0-9_]+)_table/i', $migrationName, $m)) {
        $table = $m[1];
    } else {
        // Try to find Schema::create('table' or Schema::table('table'
        if (preg_match("/Schema::create\s*\(\s*'([a-z0-9_]+)'/i", $contents, $m2)) {
            $table = $m2[1];
        } elseif (preg_match('/Schema::table\s*\(\s*\'([a-z0-9_]+)\'/i', $contents, $m3)) {
            $table = $m3[1];
        } else {
            echo "Could not infer table for migration $migrationName, skipping.\n";
            continue;
        }
    }

    // Check if table exists
    $has = DB::getSchemaBuilder()->hasTable($table);
    if ($has) {
        echo "Table '$table' already exists. Marking migration '$migrationName' as run...\n";
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => 1,
        ]);
        $marked++;
    } else {
        echo "Table '$table' does not exist, will run migration: $migrationName\n";
    }
}

echo "Done. Marked $marked migrations.\n";
