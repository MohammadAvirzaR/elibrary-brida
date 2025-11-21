<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'authors')) {
                $table->dropColumn('authors');
            }
            if (Schema::hasColumn('documents', 'supervisors')) {
                $table->dropColumn('supervisors');
            }
            if (Schema::hasColumn('documents', 'attachments')) {
                $table->dropColumn('attachments');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'authors')) {
                $table->json('authors')->nullable();
            }
            if (!Schema::hasColumn('documents', 'supervisors')) {
                $table->json('supervisors')->nullable();
            }
            if (!Schema::hasColumn('documents', 'attachments')) {
                $table->json('attachments')->nullable();
            }
        });
    }
};
