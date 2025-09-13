<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

echo "DB_CONNECTION: " . env('DB_CONNECTION') . "\n";
echo "DB_HOST: " . env('DB_HOST') . "\n";
echo "DB_DATABASE: " . env('DB_DATABASE') . "\n";
echo "APP_KEY: " . (env('APP_KEY') ? 'SET' : 'NOT SET') . "\n";