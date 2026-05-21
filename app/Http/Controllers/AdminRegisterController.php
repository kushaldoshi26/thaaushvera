<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminRegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'admin_role' => 'required|in:super_admin,manager,support',
                'role' => 'sometimes|in:admin,super_admin',
            ]);

            // Map admin_role to admin_level
            $levelMap = [
                'super_admin' => 'super',
                'manager' => 'manager',
                'support' => 'staff',
            ];

            $role = $validated['role'] ?? 'admin';
            $adminLevel = $levelMap[$validated['admin_role']] ?? 'staff';

            // If super_admin role, set role accordingly
            if ($validated['admin_role'] === 'super_admin') {
                $role = 'super_admin';
                $adminLevel = 'super';
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $role,
                'admin_level' => $adminLevel,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin account created successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'admin_level' => $user->admin_level,
                    ]
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
