<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OAuthController extends Controller
{
    /**
     * Handle Google OAuth callback from frontend (token-based)
     */
    public function handleGoogleCallback(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $socialUser = \Laravel\Socialite\Facades\Socialite::driver('google')->userFromToken($request->token);
        } catch (\Exception $e) {
            Log::error('Google OAuth token validation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired Google token'
            ], 401);
        }

        return $this->handleSocialUser($socialUser, 'google');
    }

    /**
     * Link an OAuth account to the authenticated user
     */
    public function linkOAuthAccount(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:google,facebook',
            'token' => 'required|string',
        ]);

        try {
            $socialUser = \Laravel\Socialite\Facades\Socialite::driver($request->provider)->userFromToken($request->token);
        } catch (\Exception $e) {
            Log::error('OAuth link failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify social token'
            ], 401);
        }

        $user = $request->user();
        $user->update([
            'oauth_provider' => $request->provider,
            'oauth_id' => $socialUser->getId(),
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->provider) . ' account linked successfully'
        ]);
    }

    /**
     * Unlink an OAuth account from the authenticated user
     */
    public function unlinkOAuthAccount(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:google,facebook',
        ]);

        $user = $request->user();
        
        if ($user->oauth_provider !== $request->provider) {
            return response()->json([
                'success' => false,
                'message' => 'This provider is not linked to your account'
            ], 400);
        }

        $user->update([
            'oauth_provider' => null,
            'oauth_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->provider) . ' account unlinked successfully'
        ]);
    }

    /**
     * Get connected OAuth providers for authenticated user
     */
    public function getConnectedProviders(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'google' => $user->oauth_provider === 'google',
                'facebook' => $user->oauth_provider === 'facebook',
            ]
        ]);
    }

    /**
     * Handle social user — find or create, then return token
     */
    private function handleSocialUser($socialUser, $provider)
    {
        try {
            $user = User::where('oauth_provider', $provider)
                ->where('oauth_id', $socialUser->getId())
                ->orWhere('email', $socialUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    'oauth_provider' => $provider,
                    'oauth_id' => $socialUser->getId(),
                    'last_login_at' => now(),
                ]);
            } else {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'oauth_provider' => $provider,
                    'oauth_id' => $socialUser->getId(),
                    'role' => 'user',
                    'is_active' => true,
                    'last_login_at' => now(),
                ]);

                try {
                    $user->cart()->create();
                } catch (\Exception $e) {
                    Log::error('Cart creation failed for OAuth user ' . $user->id . ': ' . $e->getMessage());
                }
            }

            $token = $user->createToken('oauth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
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
                    ],
                    'token' => $token,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('OAuth user handling failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
