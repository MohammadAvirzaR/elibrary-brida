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
        Schema::table('document_authors', function (Blueprint $table) {
            // Add university_id foreign key
            if (!Schema::hasColumn('document_authors', 'university_id')) {
                $table->unsignedBigInteger('university_id')->nullable()->after('institution');
                $table->foreign('university_id')
                    ->references('id')
                    ->on('university')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_authors', function (Blueprint $table) {
            if (Schema::hasColumn('document_authors', 'university_id')) {
                $table->dropForeign(['university_id']);
                $table->dropColumn('university_id');
            }
        });
    }
};
