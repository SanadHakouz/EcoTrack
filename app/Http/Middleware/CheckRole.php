<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        // If user is not authenticated, deny access
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Check if user account is active
        if (!$user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Account is not active'
            ], 403);
        }

        // Parse roles (comma-separated)
        $allowedRoles = array_map('trim', explode(',', $roles));
        $userRole = $user->role;

        // Admin can access everything
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Check if user has one of the required roles
        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        // For moderator routes, allow admin access
        if (in_array('moderator', $allowedRoles) && $userRole === 'admin') {
            return $next($request);
        }

        // Access denied
        return response()->json([
            'success' => false,
            'message' => 'Insufficient permissions. Required role(s): ' . $roles,
            'user_role' => $userRole,
            'required_roles' => $allowedRoles
        ], 403);
    }
}