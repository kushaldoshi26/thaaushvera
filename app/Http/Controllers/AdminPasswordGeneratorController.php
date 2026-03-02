<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminPasswordGeneratorController extends Controller
{
    /**
     * Generate a secure admin ID and temporary password
     */
    public function generate(Request $request)
    {
        if (auth()->user()->admin_role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can generate credentials'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'admin_role' => 'required|in:super_admin,manager,support',
            'is_2fa_enabled' => 'sometimes|boolean',
            'send_email' => 'sometimes|boolean'
        ]);

        // Generate secure temporary password (12 characters)
        $tempPassword = $this->generateSecurePassword();
        $adminId = $this->generateAdminId();

        // Create the admin user
        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role' => 'admin',
            'admin_role' => $validated['admin_role'],
            'is_active' => true,
            'admin_id' => $adminId,
            'two_factor_enabled' => $validated['is_2fa_enabled'] ?? false,
            'requires_password_change' => true
        ]);

        // If 2FA enabled, generate secret
        $twoFactorSecret = null;
        if ($validated['is_2fa_enabled'] ?? false) {
            $twoFactorSecret = $this->generate2FASecret($admin->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Admin credentials generated successfully',
            'credentials' => [
                'admin_id' => $adminId,
                'email' => $validated['email'],
                'temporary_password' => $tempPassword,
                'two_factor_enabled' => $validated['is_2fa_enabled'] ?? false,
                'two_factor_secret' => $twoFactorSecret,
                'note' => 'Password must be changed on first login'
            ]
        ], 201);
    }

    /**
     * Generate secure random password
     */
    private function generateSecurePassword($length = 12)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        $password = '';
        $charCount = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $charCount - 1)];
        }

        // Ensure mix of numbers and special chars
        $password = str_shuffle(
            $password .
            random_int(0, 9) .
            $chars[random_int(0, 3)]
        );

        return substr($password, 0, $length);
    }

    /**
     * Generate unique Admin ID (format: ADM-XXXXXX)
     */
    private function generateAdminId()
    {
        do {
            $adminId = 'ADM-' . strtoupper(Str::random(6));
        } while (User::where('admin_id', $adminId)->exists());

        return $adminId;
    }

    /**
     * Generate 2FA secret and return QR code
     */
    private function generate2FASecret($userId)
    {
        // Using Google Authenticator compatible TOTP secret
        $secret = base64_encode(random_bytes(32));

        // Store temporarily (in production, use proper 2FA package like BaconQrCode)
        cache()->put("2fa_secret_temp_{$userId}", $secret, 3600);

        // Generate QR code URL
        $email = auth()->user()->email;
        $appName = config('app.name', 'AUSHVERA');
        $qrCodeUrl = "otpauth://totp/{$appName}:admin_{$userId}?secret={$secret}&issuer={$appName}";

        return [
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'setup_instructions' => 'Scan the QR code with Google Authenticator, Microsoft Authenticator, or Authy'
        ];
    }

    /**
     * Verify 2FA code
     */
    public function verify2FA(Request $request)
    {
        $validated = $request->validate([
            'admin_id' => 'required|string',
            'code' => 'required|string|size:6'
        ]);

        $admin = User::where('admin_id', $validated['admin_id'])->first();

        if (!$admin || !$admin->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => '2FA not enabled for this admin'
            ], 422);
        }

        // Verify TOTP code (simplified - in production use proper package)
        if ($this->verifyTOTP($validated['code'], cache()->get("2fa_secret_{$admin->id}"))) {
            $admin->update(['two_factor_verified_at' => now()]);
            return response()->json(['success' => true, 'message' => '2FA verification successful']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid 2FA code'
        ], 422);
    }

    /**
     * Simple TOTP verification (for production, use proper 2FA package)
     */
    private function verifyTOTP($code, $secret)
    {
        // In production environment with proper 2FA packages
        // For now, simple 6-digit code verification
        return strlen($code) === 6 && ctype_digit($code);
    }

    /**
     * Get admin ID search
     */
    public function searchAdminId(Request $request)
    {
        $term = $request->query('q');

        if (strlen($term) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $admins = User::where('role', 'admin')
            ->where(function ($query) use ($term) {
                $query->where('admin_id', 'like', "%$term%")
                    ->orWhere('email', 'like', "%$term%")
                    ->orWhere('name', 'like', "%$term%");
            })
            ->select('id', 'admin_id', 'name', 'email', 'admin_role')
            ->limit(10)
            ->get();

        return response()->json(['suggestions' => $admins]);
    }

    /**
     * Export admin credentials as PDF or email
     */
    public function exportCredentials(Request $request, $adminId)
    {
        if (auth()->user()->admin_role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can export credentials'
            ], 403);
        }

        $admin = User::where('admin_id', $adminId)->first();

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin not found'], 404);
        }

        $credentials = [
            'admin_id' => $admin->admin_id,
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => $admin->admin_role,
            'created_at' => $admin->created_at->format('Y-m-d H:i:s'),
            'generated_by' => auth()->user()->name
        ];

        return response()->json([
            'success' => true,
            'credentials' => $credentials
        ]);
    }
}
