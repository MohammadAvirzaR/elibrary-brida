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
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'embargo_until',
                'statement_agreed',
            ]);
        });
    }
};
