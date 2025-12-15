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
        Schema::table('documents', function (Blueprint $table) {
            // Update status enum to include 'pending'
            DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");

            // Add publisher only if not exists
            if (!Schema::hasColumn('documents', 'publisher')) {
                $table->string('publisher')->nullable();
            }

            // Add timestamps only if not exists
            if (!Schema::hasColumn('documents', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM('approved', 'rejected') DEFAULT 'approved'");
            $table->dropColumn('publisher');
            $table->dropTimestamps();
        });
    }
};
