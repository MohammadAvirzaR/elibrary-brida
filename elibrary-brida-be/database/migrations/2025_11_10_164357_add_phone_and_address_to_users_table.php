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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('contact');
            $table->text('address')->nullable()->after('phone');
            $table->string('institution')->nullable()->after('unit_name');
            $table->string('name')->nullable()->after('full_name'); // For compatibility with frontend
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'institution', 'name']);
        });
    }
};
