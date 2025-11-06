<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Testing User Credentials ===\n\n";

$user = User::where('email', 'admin@brida.com')->first();

if ($user) {
    echo "User found:\n";
    echo "  ID: {$user->id}\n";
    echo "  Name: {$user->full_name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Password Hash: " . substr($user->password, 0, 20) . "...\n\n";

    echo "Testing password 'admin123': ";
    if (Hash::check('admin123', $user->password)) {
        echo "✅ VALID\n";
    } else {
        echo "❌ INVALID\n";
        echo "\nUpdating password to 'admin123'...\n";
        // Update directly to bypass mutator
        $user->updateQuietly(['password' => Hash::make('admin123')]);
        // Or update via DB query
        \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $user->id)
            ->update(['password' => Hash::make('admin123')]);
        echo "✅ Password updated!\n";
    }
} else {
    echo "❌ User with email 'admin@brida.com' not found!\n\n";
    echo "Creating admin user...\n";

    $user = User::create([
        'full_name' => 'Admin BRIDA',
        'email' => 'admin@brida.com',
        'password' => Hash::make('admin123'),
        'role_id' => 1,
    ]);

    echo "✅ Admin user created!\n";
    echo "  ID: {$user->id}\n";
    echo "  Email: {$user->email}\n";
    echo "  Password: admin123\n";
}
