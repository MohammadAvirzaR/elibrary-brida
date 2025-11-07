<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'institution' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'full_name' => $request->name,
            'email' => $request->email,
            'unit_name' => $request->institution,
            'password' => $request->password, // Model will auto-hash via setPasswordAttribute
            'role_id' => 5, // default guest role
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Load role relationship
        $user->load('role');

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'institution' => $user->unit_name,
                'role' => $user->role->name ?? 'guest',
            ],
            'token' => $token,
        ]);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Load role relationship
        $user->load('role');

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'username' => $user->username ?? $user->full_name,
                'institution' => $user->unit_name,
                'role' => $user->role->name ?? 'guest',
            ],
            'token' => $token,
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    // PROFILE (ME)
    public function me(Request $request)
    {
        $user = $request->user();

        // Load role relationship
        $user->load('role');

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'username' => $user->username ?? $user->full_name,
                'institution' => $user->unit_name,
                'unit_name' => $user->unit_name,
                'role' => [
                    'id' => $user->role->id ?? null,
                    'name' => $user->role->name ?? 'guest',
                ],
            ],
        ]);
    }
}
