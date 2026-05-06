<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Review;
use Illuminate\Support\Facades\Log;

class AdminDocumentController extends Controller
{
    public function approve($id)
    {
        try {
            $doc = Document::findOrFail($id);
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            Log::info("Approving document {$id} by user {$user->id}");

            $doc->status = 'approved';
            $doc->save();

            // Update or create review record (only 1 per document)
            $review = Review::updateOrCreate(
                ['document_id' => $doc->id],
                [
                    'user_id' => $user->id,
                    'status_review' => 'approved',
                    'review_date' => now(),
                ]
            );

            Log::info("Review created/updated for document {$id}: Review ID = {$review->id}");

            Notification::create([
                'user_id' => $doc->user_id,
                'document_id' => $doc->id,
                'message' => "Dokumen '{$doc->title}' telah disetujui oleh admin.",
                'sent_at' => now(),
                'status' => 'unread',
            ]);

            return response()->json(['success' => true, 'message' => 'Document approved.']);
        } catch (\Exception $e) {
            Log::error("Error approving document {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $request->validate(['note' => 'nullable|string']);
            $doc = Document::findOrFail($id);
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            Log::info("Rejecting document {$id} by user {$user->id}");

            $doc->status = 'rejected';
            $doc->save();

            // Update or create review record (only 1 per document)
            $review = Review::updateOrCreate(
                ['document_id' => $doc->id],
                [
                    'user_id' => $user->id,
                    'comment' => $request->note ?? null,
                    'status_review' => 'rejected',
                    'review_date' => now(),
                ]
            );

            Log::info("Review created/updated for document {$id}: Review ID = {$review->id}");

            return response()->json(['success' => true, 'message' => 'Document rejected.']);
        } catch (\Exception $e) {
            Log::error("Error rejecting document {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
