<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthSocialController extends Controller
{
    public function redirectToProvider($provider)
    {
        // Safety check for production configuration
        if (!config("services.{$provider}.client_id") || !config("services.{$provider}.client_secret")) {
            \Illuminate\Support\Facades\Log::error("Social login attempted but {$provider} is not configured.");
            return redirect('/profile')->with('error', ucfirst($provider) . ' login is not configured on the server. Please contact administrator.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Socialite redirect error (' . $provider . '): ' . $e->getMessage());
            return redirect('/profile')->with('error', 'Social login failed. Please try again.');
        }

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
                    'oauth_provider' => $provider,
                    'oauth_id' => $socialUser->getId(),
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                    'role' => 'user',
                    'is_active' => true,
                    'last_login_at' => now(),
                ]);

                try {
                    // Initialize cart for the new user (hardened)
                    $user->cart()->create();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Cart creation failed for social user ' . $user->id . ': ' . $e->getMessage());
                }
            }

            Auth::login($user);
            return redirect('/profile');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Social callback error: ' . $e->getMessage());
            return redirect('/profile')->with('error', 'Registration failed during social login: ' . $e->getMessage());
        }
    }

    public function handleApiCallback(Request $request)
    {
        $provider = $request->input('provider');
        $token = $request->input('token');

        if (!$provider || !$token) {
            return response()->json(['success' => false, 'message' => 'Provider and token are required'], 400);
        }

        try {
            $socialUser = Socialite::driver($provider)->userFromToken($token);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('API Socialite error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Invalid or expired social token'], 401);
        }

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
                    'oauth_provider' => $provider,
                    'oauth_id' => $socialUser->getId(),
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                    'role' => 'user',
                    'is_active' => true,
                    'last_login_at' => now(),
                ]);

                try {
                    // Initialize cart for the new user (hardened)
                    $user->cart()->create();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Cart creation failed for social user ' . $user->id . ': ' . $e->getMessage());
                }
            }

            $authToken = $user->createToken('social_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Social login successful',
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
                    'token' => $authToken,
                ]
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('API Social callback error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Social authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
