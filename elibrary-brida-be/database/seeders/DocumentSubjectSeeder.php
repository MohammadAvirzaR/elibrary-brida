<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Document;
use App\Models\Subject;

class DocumentSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = Subject::pluck('id')->toArray();

        Document::all()->each(function ($doc) use ($subjects) {
            $randomSubjects = collect($subjects)->random(rand(1, 3))->all();

            foreach ($randomSubjects as $subjectId) {
                DB::table('document_subject')->insert([
                    'document_id' => $doc->id,
                    'subject_id' => $subjectId,
                ]);
            }
        });
    }
}
