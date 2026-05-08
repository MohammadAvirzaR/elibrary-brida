<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove duplicate subjects, keeping only the first occurrence
        DB::statement("
            DELETE s1
            FROM subjects s1
            INNER JOIN subjects s2
                ON s1.subject_name = s2.subject_name
                AND s1.id > s2.id
        ");

        // Add unique constraint to prevent future duplicates
        Schema::table('subjects', function (Blueprint $table) {
            $table->unique('subject_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique(['subject_name']);
        });
    }
};
