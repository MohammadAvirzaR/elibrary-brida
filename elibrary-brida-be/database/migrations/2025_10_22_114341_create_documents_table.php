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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('author');
            $table->year('year_published')->nullable();
            $table->foreignId('type_id')->nullable()->constrained('types')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->string('language')->nullable();
            $table->string('email')->nullable();
            $table->string('keywords')->nullable();
            $table->text('abstract')->nullable();
            $table->string('file_path');
            $table->dateTime('upload_date')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('license_id')->nullable()->constrained('licenses')->onDelete('set null');
            $table->string('funding_program')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('publisher')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('research_location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('access_right', ['public', 'private'])->default('public');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
