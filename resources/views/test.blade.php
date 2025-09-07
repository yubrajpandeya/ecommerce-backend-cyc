<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Page</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f0f0; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 Ecommerce Backend Test</h1>
        <p>If you can see this page, Laravel is working correctly!</p>
        
        <h2>Admin Panel</h2>
        <p><a href="/admin" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Access Admin Panel</a></p>
        
        <h2>API Test</h2>
        <p>API Base URL: <code>{{ url('/api/v1') }}</code></p>
        <p>Try: <a href="{{ url('/api/v1/categories') }}" target="_blank">{{ url('/api/v1/categories') }}</a></p>
        
        <h2>Database Status</h2>
        <p>Connected to: {{ config('database.connections.mysql.host') }}</p>
        <p>Database: {{ config('database.connections.mysql.database') }}</p>
        
        <h2>Admin Credentials</h2>
        <p>Email: admin@chooseyourcart.com<br>
        Password: 33Uvus3]L@PT</p>
    </div>
</body>
</html>
