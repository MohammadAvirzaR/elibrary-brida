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
            $table->dropColumn('funding_program');
            $table->dropColumn('supervisor');
            $table->dropColumn('research_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('documents', function (Blueprint $table) {
            $table->string('funding_program')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('research_location')->nullable();
            $table->string('keywords')->nullable();
        });
    }
};
