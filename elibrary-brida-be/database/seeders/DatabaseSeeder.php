<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan Spatie Permission seeder (roles + permissions) terlebih dahulu
        $this->call([
            SpatiePermissionSeeder::class,
            UserSeeder::class,
            SubjectsSeeder::class,
            TypesSeeder::class,
            LicensesSeeder::class,
            UnitsSeeder::class,
            UniversitySeeder::class,
        ]);
    }
}
