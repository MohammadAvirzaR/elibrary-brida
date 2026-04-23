<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SpatiePermissionSeeder extends Seeder
{
    /**
     * Seed roles and permissions using Spatie Laravel Permission.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── 1. Create permissions ────────────────────────────────────────────
        $permissions = [
            'manage_users',
            'manage_roles',
            'upload_documents',
            'review_documents',
            'approve_documents',
            'delete_documents',
            'view_analytics',
            'manage_categories',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'api']);
        }

        // ─── 2. Create / find roles ───────────────────────────────────────────
        $roles = [
            [
                'name'        => 'super_admin',
                'description' => 'Super Administrator - akses penuh ke semua fitur',
                'permissions' => $permissions, // semua permission
            ],
            [
                'name'        => 'reviewer',
                'description' => 'Reviewer - review dan menilai dokumen yang dikirim',
                'permissions' => [
                    'review_documents',
                    'view_analytics',
                ],
            ],
            [
                'name'        => 'contributor',
                'description' => 'Kontributor - mengunggah dan mengelola dokumen pribadi',
                'permissions' => [
                    'upload_documents',
                ],
            ],
            [
                'name'        => 'guest',
                'description' => 'Guest - hanya dapat melihat dokumen publik',
                'permissions' => [],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => 'api'],
                ['description' => $roleData['description']]
            );

            // Update description in case role already existed
            $role->update(['description' => $roleData['description']]);

            // Sync permissions
            $role->syncPermissions($roleData['permissions']);

            $this->command->info("Role [{$role->name}] seeded with " . count($roleData['permissions']) . " permissions.");
        }

        // Cleanup legacy admin role: migrate assigned users to super_admin then remove admin role.
        $legacyAdminRole = Role::where('name', 'admin')->first();
        if ($legacyAdminRole) {
            foreach ($legacyAdminRole->users as $user) {
                $user->syncRoles(['super_admin']);
            }
            $legacyAdminRole->delete();
            $this->command->info('Legacy role [admin] migrated to [super_admin] and removed.');
        }

        $this->command->info('✅ SpatiePermissionSeeder completed. ' . count($permissions) . ' permissions and ' . count($roles) . ' roles seeded.');
    }
}
