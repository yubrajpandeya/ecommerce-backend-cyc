<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Ecommerce API</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center font-sans">
    <div class="max-w-4xl mx-auto p-8 bg-white rounded-xl shadow-2xl">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">
            🛒 {{ config('app.name') }} - Ecommerce Backend
        </h1>
        
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Admin Panel Section -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-lg border-l-4 border-green-500">
                <h2 class="text-2xl font-semibold text-green-700 mb-4 flex items-center">
                    📊 Admin Panel 
                    <span class="ml-2 bg-green-500 text-white px-2 py-1 rounded text-sm">Ready</span>
                </h2>
                <p class="text-gray-700 mb-4">Manage your ecommerce store with the Filament admin panel:</p>
                <a href="/admin" class="inline-block bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition duration-300 font-medium">
                    Open Admin Panel
                </a>
                
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                    <strong class="text-yellow-800">Admin Credentials:</strong><br>
                    <span class="text-sm text-gray-600">
                        Email: admin@chooseyourcart.com<br>
                        Password: 33Uvus3]L@PT
                    </span>
                </div>
            </div>

            <!-- API Endpoints Section -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg border-l-4 border-blue-500">
                <h2 class="text-2xl font-semibold text-blue-700 mb-4 flex items-center">
                    🔌 API Endpoints
                    <span class="ml-2 bg-blue-500 text-white px-2 py-1 rounded text-sm">Active</span>
                </h2>
                <p class="text-gray-700 mb-4">Base URL: <code class="bg-gray-100 px-2 py-1 rounded">{{ url('/api/v1') }}</code></p>
                
                <div class="space-y-2">
                    <h3 class="font-semibold text-blue-600">🏪 Products & Categories</h3>
                    <div class="text-sm space-y-1">
                        <div class="bg-gray-100 p-2 rounded font-mono text-xs">GET /api/v1/categories</div>
                        <div class="bg-gray-100 p-2 rounded font-mono text-xs">GET /api/v1/products</div>
                        <div class="bg-gray-100 p-2 rounded font-mono text-xs">GET /api/v1/products/featured</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 grid md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h2 class="text-xl font-semibold text-gray-700 mb-3 flex items-center">
                    💾 Database Status
                    <span class="ml-2 bg-green-500 text-white px-2 py-1 rounded text-sm">Connected</span>
                </h2>
                <p class="text-gray-600">Connected to: <strong>{{ config('database.connections.mysql.host') }}</strong></p>
                <p class="text-gray-600">Database: <strong>{{ config('database.connections.mysql.database') }}</strong></p>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">⚡ Frontend Tech Stack</h2>
                <div class="space-y-1 text-gray-600">
                    <p>• Laravel + Vite</p>
                    <p>• Tailwind CSS v4</p>
                    <p>• Filament Admin Panel v3</p>
                    <p>• API-First Architecture</p>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-amber-50 border border-amber-200 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-amber-800 mb-3">📖 Quick Start</h2>
            <div class="text-amber-700 space-y-2">
                <p>1. <strong>Admin Panel:</strong> Click "Open Admin Panel" to manage your store</p>
                <p>2. <strong>API Testing:</strong> Use Postman or curl to test the API endpoints</p>
                <p>3. <strong>Frontend Development:</strong> Run <code class="bg-amber-100 px-2 py-1 rounded">npm run dev</code> for hot reloading</p>
                <p>4. <strong>Documentation:</strong> Check <code class="bg-amber-100 px-2 py-1 rounded">API_DOCUMENTATION.md</code> for detailed API docs</p>
            </div>
        </div>
    </div>
</body>
</html>
