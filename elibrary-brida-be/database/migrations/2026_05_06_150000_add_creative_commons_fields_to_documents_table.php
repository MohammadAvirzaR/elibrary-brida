<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'license_type')) {
                $table->string('license_type', 20)->nullable()->after('license_id');
            }

            if (!Schema::hasColumn('documents', 'license_version')) {
                $table->string('license_version', 10)->nullable()->after('license_type');
            }

            if (!Schema::hasColumn('documents', 'attribution_text')) {
                $table->text('attribution_text')->nullable()->after('license_version');
            }
        });

        DB::table('documents')
            ->whereNull('license_type')
            ->update(['license_type' => 'ARR']);
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('documents', 'attribution_text')) {
                $dropColumns[] = 'attribution_text';
            }

            if (Schema::hasColumn('documents', 'license_version')) {
                $dropColumns[] = 'license_version';
            }

            if (Schema::hasColumn('documents', 'license_type')) {
                $dropColumns[] = 'license_type';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
