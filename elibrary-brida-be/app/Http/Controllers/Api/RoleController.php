<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of roles with their Spatie permissions.
     */
    public function index()
    {
        try {
            $roles = Role::with('permissions')->get()->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'guard_name' => $role->guard_name,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                    'created_at' => $role->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $roles
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch roles: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch roles'
            ], 500);
        }
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'api',
                'description' => $request->description,
            ]);

            if ($request->filled('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create role: ' . $e->getMessage(), [
                'role_name' => $request->name ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create role'
            ], 500);
        }
    }

    /**
     * Update the specified role (name, description, and sync permissions).
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = Role::findOrFail($id);

            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Sync permissions using Spatie (replaces all permissions)
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions ?? []);
            }

            // Refresh role with updated permissions
            $role->load('permissions');

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to update role: ' . $e->getMessage(), [
                'role_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update role'
            ], 500);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            // Prevent deleting core system roles
            $protectedRoles = ['super_admin', 'admin', 'guest'];
            if (in_array($role->name, $protectedRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete protected system role: ' . $role->name
                ], 400);
            }

            // Check if role is still assigned to any users
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role that is still assigned to users'
                ], 400);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to delete role: ' . $e->getMessage(), [
                'role_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role'
            ], 500);
        }
    }

    /**
     * Get all available permissions from the database (Spatie).
     */
    public function permissions()
    {
        try {
            $permissions = Permission::where('guard_name', 'api')
                ->orderBy('name')
                ->get()
                ->map(fn($p) => [
                    'id'   => $p->id,
                    'name' => $p->name,
                ]);

            return response()->json([
                'success' => true,
                'data' => $permissions
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch permissions: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch permissions'
            ], 500);
        }
    }

    /**
     * Sync permissions for a specific role (PUT /roles/{id}/permissions).
     */
    public function syncPermissions(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = Role::findOrFail($id);
            $role->syncPermissions($request->permissions);
            $role->load('permissions');

            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to sync permissions: ' . $e->getMessage(), [
                'role_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions'
            ], 500);
        }
    }
}

