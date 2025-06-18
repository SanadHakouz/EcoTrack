<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Sanctum CSRF token endpoint for SPA authentication
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Optional: Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'app' => config('app.name'),
    ]);
});

// Vue.js SPA route - catch all routes and let Vue Router handle them
// This should be the LAST route as it catches everything
Route::get('/{path?}', function () {
    return view('app');
})->where('path', '.*');

