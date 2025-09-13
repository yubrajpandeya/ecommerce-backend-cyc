<?php

// Read .env file directly
$envContent = file_get_contents('.env');
echo "=== .env File Content ===\n";
echo $envContent;
echo "\n=== End of File ===\n";

// Parse manually
$lines = explode("\n", $envContent);
foreach ($lines as $line) {
    if (strpos($line, 'DB_CONNECTION=') === 0) {
        echo "Found DB_CONNECTION line: " . $line . "\n";
    }
}