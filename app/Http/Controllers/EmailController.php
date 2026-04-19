<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Mail\PromotionalMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    // ── OTP Generation ───────────────────────────────────────────────────────

    private function generateOtp(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function otpKey(string $email, string $purpose): string
    {
        return "otp:{$purpose}:" . md5(strtolower($email));
    }

    // ── Send Registration OTP ─────────────────────────────────────────────────

    public function sendRegistrationOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower($request->email);

        if (User::where('email', $email)->exists()) {
            return response()->json(['success' => false, 'message' => 'This email is already registered.'], 422);
        }

        $otp = $this->generateOtp();
        Cache::put($this->otpKey($email, 'registration'), $otp, now()->addMinutes(10));

        try {
            Mail::to($email)->send(new OtpMail($otp, 'registration'));
            return response()->json(['success' => true, 'message' => 'OTP sent to your email. Valid for 10 minutes.']);
        } catch (\Exception $e) {
            \Log::error("OTP email failed for {$email}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.'], 500);
        }
    }

    // ── Verify OTP ────────────────────────────────────────────────────────────

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email'   => 'required|email',
            'otp'     => 'required|string|size:6',
            'purpose' => 'required|in:registration,reset',
        ]);

        $email = strtolower($request->email);
        $key   = $this->otpKey($email, $request->purpose);
        $storedOtp = Cache::get($key);

        if (!$storedOtp) {
            return response()->json(['success' => false, 'message' => 'OTP expired. Please request a new one.'], 422);
        }
        if ($storedOtp !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP. Please check and try again.'], 422);
        }

        // Mark OTP as verified (store a verification token)
        $verifiedKey = "otp_verified:{$request->purpose}:" . md5($email);
        Cache::put($verifiedKey, true, now()->addMinutes(15));
        Cache::forget($key);

        return response()->json(['success' => true, 'message' => 'OTP verified successfully.']);
    }

    // ── Forgot Password — Send Reset OTP ──────────────────────────────────────

    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower($request->email);

        $user = User::where('email', $email)->first();
        if (!$user) {
            // Return success even if user doesn't exist (security)
            return response()->json(['success' => true, 'message' => 'If this email exists, an OTP has been sent.']);
        }

        $otp = $this->generateOtp();
        Cache::put($this->otpKey($email, 'reset'), $otp, now()->addMinutes(15));

        try {
            Mail::to($email)->send(new OtpMail($otp, 'reset'));
        } catch (\Exception $e) {
            \Log::error("Password reset OTP failed for {$email}: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'If this email exists, an OTP has been sent.']);
    }

    // ── Reset Password with Verified OTP ─────────────────────────────────────

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'otp'      => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower($request->email);

        // Verify OTP
        $key       = $this->otpKey($email, 'reset');
        $storedOtp = Cache::get($key);

        if (!$storedOtp || $storedOtp !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);
        Cache::forget($key);

        // Revoke all tokens for security
        try { $user->tokens()->delete(); } catch (\Exception $e) {}

        return response()->json(['success' => true, 'message' => 'Password reset successfully. Please log in.']);
    }

    // ── Admin: Send Promotional Email ─────────────────────────────────────────

    public function sendPromoEmail(Request $request)
    {
        $request->validate([
            'target'      => 'required|in:all,subscribers,specific',
            'subject'     => 'required|string|max:200',
            'headline'    => 'required|string|max:200',
            'body'        => 'required|string',
            'coupon_code' => 'nullable|string',
            'discount'    => 'nullable|numeric',
            'cta_text'    => 'nullable|string',
            'cta_url'     => 'nullable|url',
            'emails'      => 'nullable|array', // for specific target
            'emails.*'    => 'nullable|email',
        ]);

        $data = [
            'subject'     => $request->subject,
            'headline'    => $request->headline,
            'body'        => $request->body,
            'coupon_code' => $request->coupon_code,
            'discount'    => $request->discount,
            'cta_text'    => $request->cta_text ?? 'Shop Now',
            'cta_url'     => $request->cta_url ?? config('app.url') . '/products',
        ];

        // Get recipients
        $query = User::where('role', 'user')->where('is_active', true);

        if ($request->target === 'subscribers') {
            $query->whereHas('subscriptions', fn($q) => $q->where('status', 'active'));
        } elseif ($request->target === 'specific' && $request->emails) {
            $query->whereIn('email', $request->emails);
        }

        $users = $query->select('id', 'email', 'name')->get();

        if ($users->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No recipients found.'], 404);
        }

        $sent = 0;
        $failed = 0;

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new PromotionalMail($data));
                $sent++;
            } catch (\Exception $e) {
                $failed++;
                \Log::error("Promo email failed for {$user->email}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Email campaign sent! {$sent} delivered, {$failed} failed.",
            'sent'    => $sent,
            'failed'  => $failed,
        ]);
    }
}
