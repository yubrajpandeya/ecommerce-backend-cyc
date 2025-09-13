<?php
// Simple remote DB connectivity checker.
// Usage: php scripts/check_remote_db.php

function parseEnv($path)
{
    if (!file_exists($path)) {
        fwrite(STDERR, ".env file not found at $path\n");
        exit(2);
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (!str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        // strip surrounding quotes
        $v = preg_replace('/^\"|\"$|^\'|\'$/', '', $v);
        $data[$k] = $v;
    }
    return $data;
}

$env = parseEnv(__DIR__ . '/../.env');
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';

echo "Testing TCP connectivity to $host:$port...\n";
$sock = @fsockopen($host, (int)$port, $errno, $errstr, 5);
if ($sock) {
    echo "TCP: OK\n";
    fclose($sock);
} else {
    echo "TCP: FAILED ($errno) $errstr\n";
}

echo "\nTesting PDO MySQL connection (using credentials from .env)...\n";
try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $opts = [PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    $pdo = new PDO($dsn, $user, $pass, $opts);
    echo "PDO: Connected OK. Server version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
    exit(0);
} catch (Throwable $e) {
    echo "PDO: FAILED - " . $e->getMessage() . "\n";
    // Provide helpful hints
    echo "\nHints:\n";
    echo " - Ensure the remote MySQL server allows remote connections (cPanel: Remote MySQL -> add your IP).\n";
    echo " - Make sure the DB user has the proper host allowed and permissions.\n";
    echo " - If your host blocks external access, consider running migrations from the server (SSH) or use an SSH tunnel.\n";
    exit(1);
}
