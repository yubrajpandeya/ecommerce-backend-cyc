<?php
// boots Laravel and prints admin user data for debugging
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = 'admin@chooseyourcart.com';
$user = User::where('email', $email)->first();
if (! $user) {
    echo "NOT_FOUND\n";
    exit(0);
}
$userArray = $user->toArray();
// hide sensitive tokens if any
if (isset($userArray['remember_token'])) unset($userArray['remember_token']);
echo json_encode($userArray, JSON_PRETTY_PRINT) . "\n";
