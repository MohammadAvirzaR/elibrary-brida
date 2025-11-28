<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Timestamps sudah ada di create_documents_table, skip jika sudah ada
            if (!Schema::hasColumn('documents', 'created_at') && !Schema::hasColumn('documents', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }

};
