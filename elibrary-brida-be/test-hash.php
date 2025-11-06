<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Hash;

echo "=== Testing Hash Functions ===\n\n";

$password = 'admin123';

// Test 1: bcrypt
$bcryptHash = bcrypt($password);
echo "1. Using bcrypt():\n";
echo "   Hash: " . substr($bcryptHash, 0, 30) . "...\n";
echo "   Verify: " . (Hash::check($password, $bcryptHash) ? '✅ VALID' : '❌ INVALID') . "\n\n";

// Test 2: Hash::make
$hashMake = Hash::make($password);
echo "2. Using Hash::make():\n";
echo "   Hash: " . substr($hashMake, 0, 30) . "...\n";
echo "   Verify: " . (Hash::check($password, $hashMake) ? '✅ VALID' : '❌ INVALID') . "\n\n";

// Test 3: password_hash
$passwordHash = password_hash($password, PASSWORD_BCRYPT);
echo "3. Using password_hash():\n";
echo "   Hash: " . substr($passwordHash, 0, 30) . "...\n";
echo "   Verify: " . (Hash::check($password, $passwordHash) ? '✅ VALID' : '❌ INVALID') . "\n\n";

echo "Now updating user with bcrypt hash...\n";
$user = App\Models\User::where('email', 'admin@brida.com')->first();
if ($user) {
    $user->password = bcrypt('admin123');
    $user->save();
    echo "✅ Password updated with bcrypt!\n";
    echo "Verify: " . (Hash::check('admin123', $user->password) ? '✅ VALID' : '❌ INVALID') . "\n";
}
