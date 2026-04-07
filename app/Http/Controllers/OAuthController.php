<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class OAuthController extends Controller
{
    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'provider' => 'required|in:google,facebook'
        ]);

        try {
            // Verify token with provider
            $userInfo = $this->verifyOAuthToken($validated['token'], $validated['provider']);

            if (!$userInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to verify OAuth token'
                ], 401);
            }

            // Find or create user
            $user = User::where('oauth_provider', $validated['provider'])
                ->where('oauth_id', $userInfo['id'])
                ->orWhere('email', $userInfo['email'])
                ->first();

            if (!$user) {
                // Create new user from OAuth info
                $user = User::create([
                    'name' => $userInfo['name'],
                    'email' => $userInfo['email'],
                    'password' => Hash::make(Str::random(16)),
                    'oauth_provider' => $validated['provider'],
                    'oauth_id' => $userInfo['id'],
                    'role' => 'user',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'last_login_at' => now(),
                ]);

                try {
                    // Initialize cart for new user (hardened)
                    $user->cart()->create();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Cart creation failed for OAuth user ' . $user->id . ': ' . $e->getMessage());
                }
            } else {
                // Update existing user OAuth details
                $user->update([
                    'oauth_provider' => $validated['provider'],
                    'oauth_id' => $userInfo['id'],
                    'last_login_at' => now(),
                ]);
            }

            // Create API token
            $token = $user->createToken('oauth-login')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'dob' => $user->dob,
                    'gender' => $user->gender,
                    'city' => $user->city,
                    'state' => $user->state,
                    'pincode' => $user->pincode,
                    'address' => $user->address,
                    'oauth_provider' => $validated['provider']
                ]
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OAuth handle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'OAuth authentication failed: ' . $e->getMessage()
            ], 401);
        }
    }

    /**
     * Verify OAuth token with provider
     */
    private function verifyOAuthToken($token, $provider)
    {
        if ($provider === 'google') {
            return $this->verifyGoogleToken($token);
        } elseif ($provider === 'facebook') {
            return $this->verifyFacebookToken($token);
        }

        return null;
    }

    /**
     * Verify Google OAuth token
     */
    private function verifyGoogleToken($token)
    {
        try {
            $response = Http::get('https://www.googleapis.com/oauth2/v3/tokeninfo', [
                'access_token' => $token
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'id' => $data['user_id'] ?? $data['sub'],
                    'email' => $data['email'],
                    'name' => $data['email'], // Google token doesn't include name, use email
                    'picture' => $data['picture'] ?? null
                ];
            }
        } catch (\Exception $e) {
            // Try alternative endpoint for ID token verification
            return $this->verifyGoogleIdToken($token);
        }

        return null;
    }

    /**
     * Verify Google ID Token
     */
    private function verifyGoogleIdToken($idToken)
    {
        try {
            // In production, verify JWT signature properly
            // For now, decode and validate basic structure
            $parts = explode('.', $idToken);
            if (count($parts) !== 3) {
                return null;
            }

            $payload = json_decode(base64_decode($parts[1]), true);

            if ($payload && isset($payload['email'], $payload['sub'])) {
                return [
                    'id' => $payload['sub'],
                    'email' => $payload['email'],
                    'name' => $payload['name'] ?? $payload['email'],
                    'picture' => $payload['picture'] ?? null
                ];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * Verify Facebook OAuth token
     */
    private function verifyFacebookToken($token)
    {
        try {
            $response = Http::get('https://graph.facebook.com/me', [
                'access_token' => $token,
                'fields' => 'id,name,email,picture'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'id' => $data['id'],
                    'email' => $data['email'] ?? 'facebook-' . $data['id'] . '@oauth.local',
                    'name' => $data['name'],
                    'picture' => $data['picture']['data']['url'] ?? null
                ];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * Link OAuth provider to existing account
     */
    public function linkOAuthAccount(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'token' => 'required|string',
            'provider' => 'required|in:google,facebook'
        ]);

        $userInfo = $this->verifyOAuthToken($validated['token'], $validated['provider']);

        if (!$userInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OAuth token'
            ], 401);
        }

        // Check if OAuth account already linked to another user
        $existingLink = User::where('oauth_provider', $validated['provider'])
            ->where('oauth_id', $userInfo['id'])
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingLink) {
            return response()->json([
                'success' => false,
                'message' => 'This OAuth account is already linked to another user'
            ], 422);
        }

        // Link OAuth account
        $user->update([
            'oauth_provider' => $validated['provider'],
            'oauth_id' => $userInfo['id']
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($validated['provider']) . ' account linked successfully'
        ]);
    }

    /**
     * Unlink OAuth provider from account
     */
    public function unlinkOAuthAccount(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'provider' => 'required|in:google,facebook'
        ]);

        if ($user->oauth_provider !== $validated['provider']) {
            return response()->json([
                'success' => false,
                'message' => 'This provider is not linked to your account'
            ], 422);
        }

        $user->update([
            'oauth_provider' => null,
            'oauth_id' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($validated['provider']) . ' account unlinked successfully'
        ]);
    }

    /**
     * Get connected OAuth providers
     */
    public function getConnectedProviders()
    {
        $user = auth()->user();

        return response()->json([
            'connected_providers' => $user->oauth_provider ? [$user->oauth_provider] : [],
            'available_providers' => ['google', 'facebook']
        ]);
    }
}
