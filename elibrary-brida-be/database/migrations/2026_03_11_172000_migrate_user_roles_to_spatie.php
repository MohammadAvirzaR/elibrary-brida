<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Post-migration: Migrasi data role user dari kolom role_id ke tabel model_has_roles Spatie.
 * Kemudian hapus kolom role_id dari tabel users.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Migrasi user roles dari role_id ke model_has_roles (Spatie)
        $users = DB::table('users')->whereNotNull('role_id')->get(['id', 'role_id']);

        foreach ($users as $user) {
            $roleExists = DB::table('roles')->where('id', $user->role_id)->exists();
            if ($roleExists) {
                // Cek apakah sudah ada entry di model_has_roles
                $exists = DB::table('model_has_roles')
                    ->where('role_id', $user->role_id)
                    ->where('model_id', $user->id)
                    ->where('model_type', 'App\\Models\\User')
                    ->exists();

                if (!$exists) {
                    DB::table('model_has_roles')->insert([
                        'role_id'    => $user->role_id,
                        'model_type' => 'App\\Models\\User',
                        'model_id'   => $user->id,
                    ]);
                }
            }
        }

        // 2. Drop foreign key role_id dari users dan hapus kolomnya
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraint terlebih dahulu
            $table->dropForeign(['role_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });
    }

    public function down(): void
    {
        // Kembalikan kolom role_id ke users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });

        // Kembalikan data role_id dari model_has_roles
        $userRoles = DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->get(['model_id', 'role_id']);

        foreach ($userRoles as $userRole) {
            DB::table('users')
                ->where('id', $userRole->model_id)
                ->update(['role_id' => $userRole->role_id]);
        }
    }
};
