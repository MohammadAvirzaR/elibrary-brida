<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "Creating Super Admin User...\n\n";

// Update atau create super admin dengan password yang benar
$superAdminRoleId = DB::table('roles')->where('name', 'super_admin')->value('id');

if (!$superAdminRoleId) {
    echo "Error: Role super_admin tidak ditemukan!\n";
    echo "Jalankan: php artisan db:seed --class=RoleSeeder\n";
    exit(1);
}

// Hash password langsung dengan Hash::make untuk menghindari double hashing
$hashedPassword = Hash::make('admin123');

// Update or insert super admin
DB::table('users')->updateOrInsert(
    ['email' => 'admin@brida.com'],
    [
        'role_id' => $superAdminRoleId,
        'full_name' => 'Super Admin BRIDA',
        'username' => 'superadmin',
        'email' => 'admin@brida.com',
        'password' => $hashedPassword,
        'unit_name' => 'BRIDA Sulawesi Tenggara',
        'updated_at' => now(),
        'created_at' => now(),
    ]
);

echo "✅ Super Admin User created/updated successfully!\n\n";
echo "Login Credentials:\n";
echo "==================\n";
echo "Email    : admin@brida.com\n";
echo "Password : admin123\n";
echo "Role     : super_admin\n";
echo "==================\n\n";

// Verify the user
$user = DB::table('users')
    ->join('roles', 'users.role_id', '=', 'roles.id')
    ->where('users.email', 'admin@brida.com')
    ->select('users.*', 'roles.name as role_name')
    ->first();

if ($user) {
    echo "User Details:\n";
    echo "- ID: {$user->id}\n";
    echo "- Name: {$user->full_name}\n";
    echo "- Email: {$user->email}\n";
    echo "- Role: {$user->role_name}\n";
    echo "- Password Hash: " . substr($user->password, 0, 20) . "...\n\n";

    // Test password verification
    if (Hash::check('admin123', $user->password)) {
        echo "✅ Password verification: SUCCESS\n";
    } else {
        echo "❌ Password verification: FAILED\n";
    }
} else {
    echo "❌ Error: User not found after creation!\n";
}

echo "\n";
