<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            // For API requests return JSON, for web requests redirect
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return redirect()->route('admin.login')->withErrors(['email' => 'You do not have admin access.']);
        }

        // Block deactivated admin accounts
        if ($user->is_active === false) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Your admin account has been deactivated.'], 403);
            }
            Auth::logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'Your account has been deactivated. Contact super admin.']);
        }

        return $next($request);
    }
}
