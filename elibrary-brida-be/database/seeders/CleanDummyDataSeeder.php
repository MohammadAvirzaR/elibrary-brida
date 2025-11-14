<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

class CleanDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder removes all dummy documents from the database.
     * Only use this in development to clean up test data.
     */
    public function run(): void
    {
        $this->command->info('Removing dummy documents...');

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Delete all related data
        DB::table('reviews')->truncate();
        DB::table('document_subject')->truncate();
        DB::table('documents')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('All dummy documents have been removed.');
        $this->command->info('Database is ready for real document uploads.');
    }
}
