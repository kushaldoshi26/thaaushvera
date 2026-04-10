<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Block deactivated admin accounts
        if ($user->is_active === false) {
            return response()->json(['success' => false, 'message' => 'Your admin account has been deactivated. Contact the super admin.'], 403);
        }

        return $next($request);
    }
}
