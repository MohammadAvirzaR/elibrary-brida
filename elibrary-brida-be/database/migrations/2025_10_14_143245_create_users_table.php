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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->string('full_name');
            $table->string('username')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('sso_id')->nullable();
            $table->string('unit_name')->nullable(); // tanpa tabel unit
            $table->string('contact')->nullable();
            $table->text('bio')->nullable();
            $table->string('membership_proof')->nullable();
            $table->enum('profession',  [
                'pelajar_mahasiswa', 
                'dosen_tenaga_pendidik', 
                'pns_tni_polri_dsb', 
                'peneliti', 
                'pegawai_brin_brida'
            ])->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
