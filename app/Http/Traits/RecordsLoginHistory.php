<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait RecordsLoginHistory
{
    /**
     * Record a login event in the login_history table.
     *
     * @param int    $userId      The user ID
     * @param string $loginMethod The login method (email, google, facebook, api, otp)
     */
    protected function recordLoginHistory(int $userId, string $loginMethod = 'email'): void
    {
        try {
            DB::table('login_history')->insert([
                'user_id'      => $userId,
                'ip_address'   => request()->ip(),
                'login_time'   => now(),
                'login_method' => $loginMethod,
                'user_agent'   => request()->userAgent(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        } catch (\Exception $e) {
            // Don't crash the login flow if logging fails
            Log::warning('Failed to record login history: ' . $e->getMessage());
        }
    }
}
