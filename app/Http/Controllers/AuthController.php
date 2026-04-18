<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
                'phone' => 'nullable|string',
                'dob' => 'nullable|date',
                'gender' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'pincode' => 'nullable|string',
                'address' => 'nullable|string',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'dob' => $validated['dob'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'pincode' => $validated['pincode'] ?? null,
                'address' => $validated['address'] ?? null,
                'role' => 'user',
            ]);

            try {
                // Initialize cart for new user
                $user->cart()->create();
            } catch (\Exception $e) {
                // Log cart creation failure but don't crash registration
                \Illuminate\Support\Facades\Log::error('Cart creation failed for user ' . $user->id . ': ' . $e->getMessage());
            }

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
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
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(fn($v) => implode(' ', $v), $e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Provide exact database error if possible
            $errorMsg = 'Registration failed: ' . $e->getMessage();
            if (str_contains($e->getMessage(), 'column not found') || str_contains($e->getMessage(), 'no such column')) {
                $errorMsg .= ' (Database schema mismatch. Please run php artisan migrate.)';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMsg,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => ['email' => ['The provided credentials (email or password) are incorrect.']]
                ], 422);
            }

            // Check if account is active
            if (($user->role === 'admin' || $user->role === 'super_admin') && !$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Contact super admin.'
                ], 403);
            }

            // Update last login
            $user->update(['last_login_at' => now()]);

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

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
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(fn($v) => implode(' ', $v), $e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current user
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
                'phone' => $request->user()->phone,
                'city' => $request->user()->city,
                'state' => $request->user()->state,
                'gender' => $request->user()->gender,
                'dob' => $request->user()->dob,
                'pincode' => $request->user()->pincode,
                'address' => $request->user()->address,
            ]
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
