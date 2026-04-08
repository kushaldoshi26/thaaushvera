<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthSocialController extends Controller
{
    /**
     * Redirect to the provider's authentication page.
     */
    public function redirectToProvider($provider)
    {
        // Safety check for production configuration
        if (!config("services.{$provider}.client_id") || !config("services.{$provider}.client_secret")) {
            \Illuminate\Support\Facades\Log::error("Social login attempted but {$provider} is not configured.");
            return redirect('/profile')->with('error', ucfirst($provider) . ' login is not configured on the server. Please contact administrator.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the provider callback for web-based flows.
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Socialite redirect error ({$provider}): " . $e->getMessage());
            return redirect('/profile')->with('error', 'Authentication failed at the provider. Please try again.');
        }

        try {
            $user = $this->findOrCreateUser($socialUser, $provider);
            
            Auth::login($user);
            
            return redirect('/profile');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Social callback error: " . $e->getMessage());
            return redirect('/profile')->with('error', 'System was unable to complete your registration. please contact support.');
        }
    }

    /**
     * Handle the token-based callback for API/Mobile flows.
     */
    public function handleApiCallback(Request $request)
    {
        $provider = $request->input('provider');
        $token = $request->input('token');

        if (!$provider || !$token) {
            return response()->json(['success' => false, 'message' => 'Provider and token are required'], 400);
        }

        try {
            // This is for access tokens. If the frontend sends an ID token, this might need modification.
            $socialUser = Socialite::driver($provider)->userFromToken($token);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("API Socialite error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Invalid or expired social token'], 401);
        }

        try {
            $user = $this->findOrCreateUser($socialUser, $provider);
            $authToken = $user->createToken('social_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Social login successful',
                'data' => [
                    'user' => $user,
                    'token' => $authToken,
                ]
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("API Social callback error: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Shared logic to find or create a user based on OAuth data.
     */
    private function findOrCreateUser($socialUser, $provider)
    {
        $email = $socialUser->getEmail();
        $id = $socialUser->getId();

        // 1. Try to find by provider ID (Strongest link)
        $user = User::where('oauth_provider', $provider)
            ->where('oauth_id', $id)
            ->first();

        if ($user) {
            $user->update(['last_login_at' => now()]);
            return $user;
        }

        // 2. Try to find by email (Merge logic)
        if ($email) {
            $user = User::where('email', $email)->first();
            
            if ($user) {
                // Link this provider to the existing email account
                $user->update([
                    'oauth_provider' => $provider,
                    'oauth_id' => $id,
                    'last_login_at' => now(),
                    // Optionally update name if missing
                    'name' => $user->name ?: ($socialUser->getName() ?: $email),
                ]);
                return $user;
            }
        }

        // 3. Create a new user
        $user = User::create([
            'name' => $socialUser->getName() ?: ($email ?: 'Aushvera User'),
            'email' => $email,
            'oauth_provider' => $provider,
            'oauth_id' => $id,
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
            'role' => 'user',
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        // Initialize cart
        try {
            $user->cart()->create();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Cart creation failed for new social user {$user->id}: " . $e->getMessage());
        }

        return $user;
    }
}
