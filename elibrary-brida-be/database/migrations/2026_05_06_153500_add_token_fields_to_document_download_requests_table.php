<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_download_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('document_download_requests', 'approval_token')) {
                $table->string('approval_token', 120)->nullable()->after('status');
            }

            if (!Schema::hasColumn('document_download_requests', 'owner_action_at')) {
                $table->timestamp('owner_action_at')->nullable()->after('approval_token');
            }

            if (!Schema::hasColumn('document_download_requests', 'download_token')) {
                $table->string('download_token', 120)->nullable()->after('owner_action_at');
            }

            if (!Schema::hasColumn('document_download_requests', 'download_token_expires_at')) {
                $table->timestamp('download_token_expires_at')->nullable()->after('download_token');
            }

            if (!Schema::hasColumn('document_download_requests', 'downloaded_at')) {
                $table->timestamp('downloaded_at')->nullable()->after('download_token_expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_download_requests', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('document_download_requests', 'downloaded_at')) {
                $dropColumns[] = 'downloaded_at';
            }
            if (Schema::hasColumn('document_download_requests', 'download_token_expires_at')) {
                $dropColumns[] = 'download_token_expires_at';
            }
            if (Schema::hasColumn('document_download_requests', 'download_token')) {
                $dropColumns[] = 'download_token';
            }
            if (Schema::hasColumn('document_download_requests', 'owner_action_at')) {
                $dropColumns[] = 'owner_action_at';
            }
            if (Schema::hasColumn('document_download_requests', 'approval_token')) {
                $dropColumns[] = 'approval_token';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
