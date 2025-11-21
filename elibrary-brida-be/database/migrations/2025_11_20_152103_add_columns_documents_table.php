<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {

            // Untuk hak akses Embargo
            if (!Schema::hasColumn('documents', 'embargo_until')) {
                $table->date('embargo_until')->nullable()->after('access_right');
            }

            // Checklist pernyataan
            if (!Schema::hasColumn('documents', 'statement_agreed')) {
                $table->boolean('statement_agreed')->default(false)->after('license_id');
            }

            // Multi author
            if (!Schema::hasColumn('documents', 'authors')) {
                $table->json('authors')->nullable()->after('email');
            }

            // Multi supervisor (opsional)
            if (!Schema::hasColumn('documents', 'supervisors')) {
                $table->json('supervisors')->nullable()->after('authors');
            }

            // Attachments opsional
            if (!Schema::hasColumn('documents', 'attachments')) {
                $table->json('attachments')->nullable()->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'embargo_until',
                'statement_agreed',
                'authors',
                'supervisors',
                'attachments'
            ]);
        });
    }
};
