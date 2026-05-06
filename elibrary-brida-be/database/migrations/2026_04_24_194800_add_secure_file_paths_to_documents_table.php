<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'original_path')) {
                $table->string('original_path')->nullable()->after('file_path');
            }

            if (!Schema::hasColumn('documents', 'preview_path')) {
                $table->string('preview_path')->nullable()->after('original_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'preview_path')) {
                $table->dropColumn('preview_path');
            }

            if (Schema::hasColumn('documents', 'original_path')) {
                $table->dropColumn('original_path');
            }
        });
    }
};

