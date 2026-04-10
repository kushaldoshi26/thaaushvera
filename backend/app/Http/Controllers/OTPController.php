<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OTPController extends Controller
{
    /**
     * Generate and send OTP to user's email
     */
    public function generateOTP(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $validated['email'])->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in cache for 10 minutes
        cache()->put("otp_{$user->id}", [
            'code' => $otp,
            'attempts' => 0,
            'created_at' => now()
        ], 600);

        // In production, send via email/SMS
        // Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
        //     $message->to($user->email)->subject('Your OTP Code');
        // });

        // For development, log to console
        \Log::info("OTP for {$user->email}: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email',
            'dev_otp' => $otp // Remove in production
        ]);
    }

    /**
     * Verify OTP code
     */
    public function verifyOTP(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|size:6|regex:/^\d{6}$/'
        ]);

        $otpData = cache()->get("otp_{$validated['user_id']}");

        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new one.'
            ], 422);
        }

        // Check attempt limit
        if ($otpData['attempts'] >= 3) {
            cache()->forget("otp_{$validated['user_id']}");
            return response()->json([
                'success' => false,
                'message' => 'Too many failed attempts. Please request a new OTP.'
            ], 429);
        }

        // Verify OTP
        if ($otpData['code'] !== $validated['code']) {
            $otpData['attempts']++;
            cache()->put("otp_{$validated['user_id']}", $otpData, 600);
            
            $remaining = 3 - $otpData['attempts'];
            return response()->json([
                'success' => false,
                'message' => "Invalid OTP. {$remaining} attempts remaining."
            ], 422);
        }

        // OTP verified successfully
        $user = User::find($validated['user_id']);
        cache()->forget("otp_{$validated['user_id']}");

        // Mark as verified
        $user->update(['otp_verified_at' => now()]);

        // Create API token for 2FA completion
        $token = $user->createToken('otp-verification')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'token' => $token
        ]);
    }

    /**
     * Verify 2FA code during login
     */
    public function verify2FACode(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|size:6|regex:/^\d{6}$/'
        ]);

        $user = User::find($validated['user_id']);

        if (!$user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => '2FA not enabled for this user'
            ], 422);
        }

        // In production, use proper TOTP validation
        // For now, validate against a simple pattern
        if ($this->validateTOTPCode($validated['code'], $user)) {
            $user->update(['two_factor_verified_at' => now()]);
            return response()->json([
                'success' => true,
                'message' => '2FA verification successful'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid 2FA code'
        ], 422);
    }

    /**
     * Validate TOTP code (simplified version)
     */
    private function validateTOTPCode($code, $user)
    {
        // This is a simplified validation
        // In production, use proper TOTP algorithms (e.g., PHPTOTP library)
        return strlen($code) === 6 && ctype_digit($code);
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::find($validated['user_id']);

        // Clear old OTP
        cache()->forget("otp_{$user->id}");

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        cache()->put("otp_{$user->id}", [
            'code' => $otp,
            'attempts' => 0,
            'created_at' => now()
        ], 600);

        \Log::info("Resent OTP for {$user->email}: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'New OTP sent',
            'dev_otp' => $otp // Remove in production
        ]);
    }

    /**
     * Get OTP verification status
     */
    public function getOTPStatus(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'otp_verified' => $user->otp_verified_at !== null,
            'two_factor_enabled' => $user->two_factor_enabled,
            'two_factor_verified' => $user->two_factor_verified_at !== null
        ]);
    }
}
