<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    // Routes that require SUPER ADMIN only
    const SUPER_ONLY_ROUTES = [
        'admin/admins',
        'admin/credentials',
        'admin/register',
        'admin/verify-2fa',
    ];

    // Routes that require MANAGER or above (not staff)
    const MANAGER_ROUTES = [
        'admin/users',
        'admin/coupons',
        'admin/categories',
        'admin/reviews',
        'admin/subscriptions',
        'admin/user-subscriptions',
        'admin/banners',
        'admin/pricing',
        'admin/inventory',
        'admin/activity-logs',
        'admin/analytics',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized. Admin access required.'], 403);
            }
            return redirect()->route('admin.login')->withErrors(['email' => 'You do not have admin access.']);
        }

        // Block deactivated accounts
        if ($user->is_active === false) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Your account has been deactivated.'], 403);
            }
            Auth::logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'Your account has been deactivated. Contact super admin.']);
        }

        $level = $user->admin_level ?? ($user->role === 'super_admin' ? 'super' : 'staff');
        $path  = $request->path();

        // ── Super admin only routes ──────────────────────────────────────────
        foreach (self::SUPER_ONLY_ROUTES as $superRoute) {
            if (str_contains($path, $superRoute)) {
                if ($user->role !== 'super_admin') {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Only Super Admin can access this feature.'
                        ], 403);
                    }
                    return redirect()->route('admin.dashboard')->withErrors(['error' => 'Only Super Admin can access this.']);
                }
            }
        }

        // ── Manager-level routes ─────────────────────────────────────────────
        foreach (self::MANAGER_ROUTES as $managerRoute) {
            if (str_contains($path, $managerRoute)) {
                if (!in_array($level, ['super', 'manager']) && $user->role !== 'super_admin') {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient permissions. Manager or above required.'
                        ], 403);
                    }
                    return redirect()->route('admin.dashboard')->withErrors(['error' => 'You do not have permission to access this area.']);
                }
            }
        }

        return $next($request);
    }
}
