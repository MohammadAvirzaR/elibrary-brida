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
        Schema::create('contributor_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('message')->nullable(); // Alasan user ingin jadi kontributor
            $table->text('admin_notes')->nullable(); // Catatan admin saat approve/reject
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null'); // Admin yang handle request
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributor_requests');
    }
};
