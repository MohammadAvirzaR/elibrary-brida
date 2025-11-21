<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {

            // Hapus kolom single-value
            $table->dropColumn(['author', 'supervisor', 'abstract']);

            // Tambah abstrak 2 bahasa
            $table->text('abstract_id')->nullable();
            $table->text('abstract_en')->nullable();

            // Ubah status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Restore (opsional)
        });
    }
};
