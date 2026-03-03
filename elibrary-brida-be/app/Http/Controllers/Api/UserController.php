<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        try {
            $users = User::with('role')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->full_name,
                    'email' => $user->email,
                    'institution' => $user->institution,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'role' => $user->role ? $user->role->name : 'Guest',
                    'role_id' => $user->role_id,
                    'created_at' => $user->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        try {
            $user = User::with('role')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->full_name,
                    'email' => $user->email,
                    'institution' => $user->institution,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'role' => $user->role ? $user->role->name : 'Guest',
                    'role_id' => $user->role_id,
                    'created_at' => $user->created_at,
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'institution' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
        
                'full_name' => $request->name, // Sync with name
                'email' => $request->email,
                'institution' => $request->institution,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->full_name,
                    'email' => $user->email,
                    'institution' => $user->institution,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'role' => $user->role ? $user->role->name : 'Guest',
                    'role_id' => $user->role_id,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'institution' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'role_id' => 'sometimes|integer|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($id);

            $updateData = [];
            
            // Handle name field - sync between name and full_name
            if ($request->has('name') && $request->filled('name')) {
                $updateData['name'] = $request->name;
                $updateData['full_name'] = $request->name;
            }
            
            if ($request->has('email') && $request->filled('email')) {
                $updateData['email'] = $request->email;
            }
            
            if ($request->has('institution') && $request->filled('institution')) {
                $updateData['institution'] = $request->institution;
            }
            
            if ($request->has('phone') && $request->filled('phone')) {
                $updateData['phone'] = $request->phone;
            }
            
            if ($request->has('address') && $request->filled('address')) {
                $updateData['address'] = $request->address;
            }
            
            if ($request->has('role_id') && $request->filled('role_id')) {
                $updateData['role_id'] = $request->role_id;
            }
            
            // Hash password only if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Only update if there's data to update
            if (!empty($updateData)) {
                $user->update($updateData);
            }

            // Refresh user data
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->full_name,
                    'email' => $user->email,
                    'institution' => $user->institution,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'role' => $user->role ? $user->role->name : 'Guest',
                    'role_id' => $user->role_id,
                    'created_at' => $user->created_at,
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('User update failed for ID ' . $id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting own account
            $currentUser = Auth::user();
            if ($currentUser && $user->id === $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
