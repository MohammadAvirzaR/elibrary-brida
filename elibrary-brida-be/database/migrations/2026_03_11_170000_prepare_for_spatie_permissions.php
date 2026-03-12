<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pre-migration: Menyiapkan database sebelum Spatie Permission dijalankan.
 * - Drop kolom permissions (JSON) pada tabel roles (akan digantikan oleh role_has_permissions)
 * - Drop tabel privilages lama
 * - Drop role_id dari users (akan digantikan oleh model_has_roles)
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop kolom permissions JSON dari tabel roles (tidak diperlukan lagi)
        if (Schema::hasColumn('roles', 'permissions')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('permissions');
            });
        }

        // 2. Drop tabel privilages lama (akan digantikan role_has_permissions dari Spatie)
        Schema::dropIfExists('role_privilege');
        Schema::dropIfExists('privilages');

        // 3. Tambahkan guard_name ke tabel roles yang sudah ada
        //    Ini agar Spatie bisa memodifikasi tabel yang sudah ada
        if (!Schema::hasColumn('roles', 'guard_name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('guard_name')->default('api')->after('description');
            });
        }
    }

    public function down(): void
    {
        // Kembalikan kolom permissions JSON jika rollback
        if (!Schema::hasColumn('roles', 'permissions')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->json('permissions')->nullable()->after('description');
            });
        }

        if (Schema::hasColumn('roles', 'guard_name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('guard_name');
            });
        }
    }
};
