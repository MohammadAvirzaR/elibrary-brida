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
            DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
            $table->string('publisher')->nullable()->after('supervisor');
            $table->timestamps();
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
