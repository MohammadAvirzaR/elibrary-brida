<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Check and drop columns if they exist (untuk compatibility)
            if (Schema::hasColumn('documents', 'author')) {
                $table->dropColumn('author');
            }
            if (Schema::hasColumn('documents', 'supervisor')) {
                $table->dropColumn('supervisor');
            }
            if (Schema::hasColumn('documents', 'abstract')) {
                $table->dropColumn('abstract');
            }

            // Tambah abstrak 2 bahasa jika belum ada
            if (!Schema::hasColumn('documents', 'abstract_id')) {
                $table->text('abstract_id')->nullable();
            }
            if (!Schema::hasColumn('documents', 'abstract_en')) {
                $table->text('abstract_en')->nullable();
            }

            // Ubah status enum
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Restore columns
            if (!Schema::hasColumn('documents', 'author')) {
                $table->string('author')->nullable();
            }
            if (!Schema::hasColumn('documents', 'supervisor')) {
                $table->string('supervisor')->nullable();
            }
            if (!Schema::hasColumn('documents', 'abstract')) {
                $table->text('abstract')->nullable();
            }

            if (Schema::hasColumn('documents', 'abstract_id')) {
                $table->dropColumn('abstract_id');
            }
            if (Schema::hasColumn('documents', 'abstract_en')) {
                $table->dropColumn('abstract_en');
            }
        });
    }
};
