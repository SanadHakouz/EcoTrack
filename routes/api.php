<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Test endpoint to verify API is working
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'EcoTrack API is working!',
        'version' => '1.0.0',
        'timestamp' => now(),
        'environment' => app()->environment(),
    ]);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
    ]);
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Password reset routes (public - no auth required)
    Route::post('password/request-reset', [App\Http\Controllers\Api\UserController::class, 'requestPasswordReset']);
    Route::post('password/verify-code', [App\Http\Controllers\Api\UserController::class, 'verifyResetCode']);
    Route::post('password/reset', [App\Http\Controllers\Api\UserController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authentication Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication Protected Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    /*
    |--------------------------------------------------------------------------
    | User Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('user')->group(function () {
        Route::get('profile', [App\Http\Controllers\Api\UserController::class, 'profile']);
        Route::put('profile', [App\Http\Controllers\Api\UserController::class, 'updateProfile']);
        Route::post('profile/avatar', [App\Http\Controllers\Api\UserController::class, 'uploadAvatar']);
        Route::delete('profile/avatar', [App\Http\Controllers\Api\UserController::class, 'deleteAvatar']);
        // Future: password reset, account deactivation
        // Route::put('password', [UserController::class, 'updatePassword']);
        // Route::delete('account', [UserController::class, 'deactivateAccount']);
    });

    /*
    |--------------------------------------------------------------------------
    | Emissions Routes (Future Implementation)
    |--------------------------------------------------------------------------
    */
    Route::prefix('emissions')->group(function () {
        // Route::get('/', [EmissionController::class, 'index']);
        // Route::post('/', [EmissionController::class, 'store']);
        // Route::get('{emission}', [EmissionController::class, 'show']);
        // Route::put('{emission}', [EmissionController::class, 'update']);
        // Route::delete('{emission}', [EmissionController::class, 'destroy']);
        // Route::get('analytics/summary', [EmissionController::class, 'getSummary']);
    });

    /*
    |--------------------------------------------------------------------------
    | Posts & Social Routes (Future Implementation)
    |--------------------------------------------------------------------------
    */
    Route::prefix('posts')->group(function () {
        // Route::get('/', [PostController::class, 'index']);
        // Route::post('/', [PostController::class, 'store']);
        // Route::get('{post}', [PostController::class, 'show']);
        // Route::put('{post}', [PostController::class, 'update']);
        // Route::delete('{post}', [PostController::class, 'destroy']);
        // Route::post('{post}/react', [PostController::class, 'react']);
    });

    /*
    |--------------------------------------------------------------------------
    | Social Features Routes (Future Implementation)
    |--------------------------------------------------------------------------
    */
    Route::prefix('social')->group(function () {
        // Route::post('follow/{user}', [SocialController::class, 'follow']);
        // Route::delete('unfollow/{user}', [SocialController::class, 'unfollow']);
        // Route::get('followers', [SocialController::class, 'getFollowers']);
        // Route::get('following', [SocialController::class, 'getFollowing']);
    });

    /*
    |--------------------------------------------------------------------------
    | Notifications Routes (Future Implementation)
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->group(function () {
        // Route::get('/', [NotificationController::class, 'index']);
        // Route::put('{notification}/read', [NotificationController::class, 'markAsRead']);
        // Route::put('read-all', [NotificationController::class, 'markAllAsRead']);
        // Route::delete('{notification}', [NotificationController::class, 'destroy']);
        // Route::get('unread-count', [NotificationController::class, 'getUnreadCount']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Future Implementation)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Route::get('users', [UserManagementController::class, 'index']);
        // Route::put('users/{user}/role', [UserManagementController::class, 'updateRole']);
        // Route::put('users/{user}/status', [UserManagementController::class, 'updateStatus']);
        // Route::get('analytics/overview', [UserManagementController::class, 'getSystemAnalytics']);
    });

    /*
    |--------------------------------------------------------------------------
    | Moderator Routes (Future Implementation)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:moderator,admin')->prefix('moderator')->group(function () {
        // Route::get('reports', [ReportController::class, 'index']);
        // Route::put('reports/{report}', [ReportController::class, 'update']);
        // Route::get('posts/reported', [PostController::class, 'getReportedPosts']);
    });
});

/*
|--------------------------------------------------------------------------
| Fallback Route for API
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'available_endpoints' => [
            'GET /api/test',
            'GET /api/health',
            'POST /api/auth/register',
            'POST /api/auth/login',
            'POST /api/auth/logout (protected)',
            'GET /api/auth/me (protected)',
        ]
    ], 404);
});
