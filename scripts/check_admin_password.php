<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$email = 'admin@chooseyourcart.com';
$row = DB::table('users')->where('email', $email)->first();
if (! $row) {
    echo "NOT_FOUND\n";
    exit(0);
}
// print full row
echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
$passwordToTest = '33Uvus3]L@PT';
$matches = Hash::check($passwordToTest, $row->password);
echo "password_matches: " . ($matches ? 'true' : 'false') . "\n";
