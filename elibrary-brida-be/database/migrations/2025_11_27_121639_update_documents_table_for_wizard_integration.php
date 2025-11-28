<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Rename abstract to abstract_id for Indonesian version
            if (Schema::hasColumn('documents', 'abstract')) {
                $table->renameColumn('abstract', 'abstract_id');
            }

            // Add abstract_en if not exists
            if (!Schema::hasColumn('documents', 'abstract_en')) {
                $table->text('abstract_en')->nullable()->after('abstract_id');
            }

            // Add subject field (string) for simple subject entry from wizard
            if (!Schema::hasColumn('documents', 'subject')) {
                $table->string('subject')->nullable()->after('keywords');
            }

            // Add supervisor field if not exists (simple string field)
            if (!Schema::hasColumn('documents', 'supervisor')) {
                $table->string('supervisor')->nullable()->after('funding_program');
            }

            // Add advisor field as alternative to supervisor relation
            if (!Schema::hasColumn('documents', 'advisor')) {
                $table->string('advisor')->nullable()->after('supervisor');
            }

            // Update status enum to include 'submitted' status
            $table->enum('status', ['pending', 'approved', 'rejected', 'submitted'])->default('pending')->change();

            // Add translated_abstract for wizard step 2 translation feature
            if (!Schema::hasColumn('documents', 'translated_abstract')) {
                $table->text('translated_abstract')->nullable()->after('abstract_en');
            }

            // Ensure access_right includes all needed values
            $table->enum('access_right', ['public', 'private', 'internal', 'embargo'])->default('public')->change();

            // Add embargo_until if not exists
            if (!Schema::hasColumn('documents', 'embargo_until')) {
                $table->date('embargo_until')->nullable()->after('access_right');
            }

            // Add statement_agreed for license acceptance
            if (!Schema::hasColumn('documents', 'statement_agreed')) {
                $table->boolean('statement_agreed')->default(false)->after('embargo_until');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Remove new columns
            if (Schema::hasColumn('documents', 'subject')) {
                $table->dropColumn('subject');
            }
            if (Schema::hasColumn('documents', 'advisor')) {
                $table->dropColumn('advisor');
            }
            if (Schema::hasColumn('documents', 'translated_abstract')) {
                $table->dropColumn('translated_abstract');
            }
            if (Schema::hasColumn('documents', 'embargo_until')) {
                $table->dropColumn('embargo_until');
            }
            if (Schema::hasColumn('documents', 'statement_agreed')) {
                $table->dropColumn('statement_agreed');
            }
            if (Schema::hasColumn('documents', 'abstract_en')) {
                $table->dropColumn('abstract_en');
            }

            // Rename back abstract_id to abstract
            if (Schema::hasColumn('documents', 'abstract_id')) {
                $table->renameColumn('abstract_id', 'abstract');
            }

            // Revert status enum
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();

            // Revert access_right enum
            $table->enum('access_right', ['public', 'private'])->default('public')->change();
        });
    }
};
