<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;

class DocumentsSeeder extends Seeder
{
    public function run(): void
    {
        Document::factory()->count(20)->create();
    }
}
