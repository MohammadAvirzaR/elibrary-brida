<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Helper: format a user for API responses.
     */
    private function formatUser(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->full_name,
            'email'      => $user->email,
            'institution' => $user->unit_name,
            'role'       => $user->roles->first()?->name ?? 'guest',
            'role_id'    => $user->roles->first()?->id ?? null,
            'created_at' => $user->created_at,
        ];
    }

    /**
     * Display a listing of users.
     */
    public function index()
    {
        try {
            $users = User::with('roles')->get()->map(fn($u) => $this->formatUser($u));

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
            $user = User::with('roles')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $this->formatUser($user)
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
     * Store a newly created user and assign a role.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'institution' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'role_id'  => 'required|exists:roles,id',
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
                'full_name' => $request->name,
                'email'     => $request->email,
                'unit_name' => $request->institution,
                'password'  => Hash::make($request->password),
            ]);

            // Assign role via Spatie
            $role = Role::findOrFail($request->role_id);
            $user->assignRole($role->name);

            $user->load('roles');

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data'    => $this->formatUser($user)
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
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'institution' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
            'role_id'  => 'sometimes|integer|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::with('roles')->findOrFail($id);

            $updateData = [];

            if ($request->filled('name')) {
                $updateData['full_name'] = $request->name;
            }
            if ($request->filled('email')) {
                $updateData['email'] = $request->email;
            }
            if ($request->has('institution')) {
                $updateData['unit_name'] = $request->institution;
            }
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            // Update role via Spatie syncRoles (replace all roles with new one)
            if ($request->filled('role_id')) {
                $role = Role::findOrFail($request->role_id);
                $user->syncRoles([$role->name]);
            }

            $user->refresh()->load('roles');

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data'    => $this->formatUser($user)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('User update failed for ID ' . $id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error'   => config('app.debug') ? $e->getMessage() : null
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

