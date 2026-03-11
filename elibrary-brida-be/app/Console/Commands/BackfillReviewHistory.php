<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\Review;
use Illuminate\Console\Command;

class BackfillReviewHistory extends Command
{
    protected $signature = 'review:backfill';
    protected $description = 'Buat review record untuk dokumen approved/rejected yang belum punya review record';

    public function handle(): void
    {
        $documents = Document::whereIn('status', ['approved', 'rejected'])
            ->whereDoesntHave('reviews')
            ->get();

        $this->info("Ditemukan {$documents->count()} dokumen tanpa review record.");

        foreach ($documents as $doc) {
            Review::create([
                'document_id' => $doc->id,
                'user_id' => $doc->user_id,
                'status_review' => $doc->status,
                'review_date' => $doc->updated_at ?? $doc->created_at,
            ]);
        }

        $this->info("Selesai. {$documents->count()} review record dibuat.");
    }
}
