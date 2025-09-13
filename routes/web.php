<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// In non-production environments redirect the root to the admin panel so
// developers see the real backend instead of the Laravel welcome page.
Route::get('/', function () {
    if (!app()->environment('production')) {
        return redirect('/admin');
    }

    return view('welcome');
});

// Lightweight status endpoint to quickly check DB connectivity without
// throwing fatal exceptions. Useful during dev to confirm whether the
// database is reachable.
Route::get('/status', function () {
    try {
        $dbOk = false;
        // Avoid attempting queries if the DB connection is clearly missing.
        if (config('database.default')) {
            $driver = config('database.default');
            // If the schema manager is available, check for a core table.
            if (\Schema::hasTable('migrations')) {
                $dbOk = true;
            }
        }

        return response()->json([
            'app_env' => app()->environment(),
            'db_driver' => config('database.default'),
            'db_connected' => $dbOk,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'app_env' => app()->environment(),
            'db_driver' => config('database.default'),
            'db_connected' => false,
            'error' => $e->getMessage(),
        ], 200);
    }
});

// debug-only route for testing admin login programmatically
if (app()->environment('local')) {
    Route::post('/debug-login', function (Request $request) {
        $credentials = [
            'email' => 'admin@chooseyourcart.com',
            'password' => '33Uvus3]L@PT',
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json(['ok' => true, 'user' => Auth::user()]);
        }

        return response()->json(['ok' => false, 'error' => 'invalid_credentials'], 401);
    });
}

Route::get('/home', function () {
    return view('home');
});

// Provide a named login route expected by some middleware/helpers.
// This redirects to Filament's admin login page.
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');
