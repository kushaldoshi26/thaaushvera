<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (!$clientId || !$clientSecret || $clientId === 'your-google-client-id' || $clientSecret === 'your-google-client-secret') {
            \Illuminate\Support\Facades\Log::error('Google login attempted but credentials are not configured.');
            return redirect('/profile')->with('error', 'Google login is not configured. Please set your Google OAuth credentials in the .env file.');
        }

        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'oauth_provider' => 'google',
                    'oauth_id' => $socialUser->getId(),
                    'role' => 'user',
                    'is_active' => true,
                    'last_login_at' => now(),
                ]);

                // Initialize cart for the new user
                $user->cart()->create();
            } else {
                $user->update([
                    'oauth_provider' => 'google',
                    'oauth_id' => $socialUser->getId(),
                    'last_login_at' => now(),
                ]);
            }

            Auth::login($user);
            return redirect('/profile'); // Changed to dashboard if needed

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google callback error: ' . $e->getMessage());
            return redirect('/profile')->with('error', 'Google login failed. Please try again.');
        }
    }

    public function redirectToFacebook()
    {
        $clientId = config('services.facebook.client_id');
        $clientSecret = config('services.facebook.client_secret');

        if (!$clientId || !$clientSecret || $clientId === 'your-facebook-client-id' || $clientSecret === 'your-facebook-client-secret') {
            \Illuminate\Support\Facades\Log::error('Facebook login attempted but credentials are not configured.');
            return redirect('/profile')->with('error', 'Facebook login is not configured. Please set your Facebook OAuth credentials in the .env file.');
        }

        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $socialUser = Socialite::driver('facebook')->stateless()->user();

            // Handle case where Facebook might not provide email
            $email = $socialUser->getEmail();
            if (!$email) {
                return redirect('/profile')->with('error', 'Facebook login requires email access. Please try again or use Google login.');
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $email,
                    'password' => Hash::make(Str::random(16)),
                    'oauth_provider' => 'facebook',
                    'oauth_id' => $socialUser->getId(),
                    'role' => 'user',
                    'is_active' => true,
                    'last_login_at' => now(),
                ]);

                // Initialize cart for the new user
                $user->cart()->create();
            } else {
                $user->update([
                    'oauth_provider' => 'facebook',
                    'oauth_id' => $socialUser->getId(),
                    'last_login_at' => now(),
                ]);
            }

            Auth::login($user);
            return redirect('/profile'); // Changed to dashboard if needed

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Facebook callback error: ' . $e->getMessage());
            return redirect('/profile')->with('error', 'Facebook login failed. Please try again.');
        }
    }
}
