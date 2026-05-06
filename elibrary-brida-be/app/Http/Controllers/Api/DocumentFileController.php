<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Services\DocumentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DocumentFileController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService
    ) {
    }

    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:51200',
        ]);

        $paths = $this->documentService->storeOriginalAndGeneratePreview($request->file('file'));

        $document = Document::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'file_path' => $paths['original_path'], // backward compatibility
            'original_path' => $paths['original_path'],
            'preview_path' => $paths['preview_path'],
            'upload_date' => now(),
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded with secure preview.',
            'data' => [
                'id' => $document->id,
                'title' => $document->title,
                'preview_path' => $document->preview_path,
            ],
        ], 201);
    }

    public function preview(Request $request, int $id)
    {
        $document = Document::with(['license', 'user.roles'])->findOrFail($id);

        $user = auth('sanctum')->user();
        $accessResponse = $this->checkPreviewAccess($document, $user);
        if ($accessResponse instanceof JsonResponse) {
            return $accessResponse;
        }

        $absolutePreviewPath = null;
        $previewRelativePath = $document->preview_path;

        if ($previewRelativePath) {
            $absolutePreviewPath = $this->resolveStoragePath($previewRelativePath);
        }

        // Jika preview belum ada, generate preview terbatas dari sumber PDF.
        if (!$absolutePreviewPath) {
            $sourcePath = $document->original_path ?: $document->file_path;
            if (!$sourcePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Preview source file not found.',
                ], 404);
            }

            $absoluteSourcePath = $this->resolveStoragePath($sourcePath);
            if (!$absoluteSourcePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Preview source file not found.',
                ], 404);
            }

            if (!str_ends_with(strtolower($absoluteSourcePath), '.pdf')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Preview hanya tersedia untuk file PDF.',
                ], 422);
            }

            try {
                $previewRelativePath = $this->documentService->generatePreview($sourcePath);
                $document->forceFill(['preview_path' => $previewRelativePath])->save();
                $absolutePreviewPath = $this->resolveStoragePath($previewRelativePath);
            } catch (Throwable $exception) {
                Log::error('Preview generation failed', [
                    'document_id' => $document->id,
                    'source_path' => $sourcePath,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if (!$absolutePreviewPath) {
            return response()->json([
                'success' => false,
                'message' => 'Preview dokumen gagal diproses.',
            ], 500);
        }

        // Extension hook for analytics event in future.
        $this->trackPreviewEvent($document, $user);

        $inlineName = basename($absolutePreviewPath);

        return response()->file($absolutePreviewPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $inlineName . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(Request $request, int $id)
    {
        /** @var User $user */
        $user = $request->user();
        $document = Document::with('license')->findOrFail($id);

        $accessResponse = $this->checkDownloadAccess($document, $user);
        if ($accessResponse instanceof JsonResponse) {
            return $accessResponse;
        }

        $originalRelativePath = $document->original_path ?: $document->file_path;
        if (!$originalRelativePath || !Storage::disk('local')->exists($originalRelativePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Original document file not found.',
            ], 404);
        }

        $absoluteOriginalPath = Storage::disk('local')->path($originalRelativePath);
        $safeName = str_replace(['/', '\\'], '-', $document->title) . '.pdf';

        // Extension hook for download request workflow / analytics.
        $this->trackDownloadEvent($document, $user);

        return response()->download($absoluteOriginalPath, $safeName, [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function checkPreviewAccess(Document $document, ?User $user): ?JsonResponse
    {
        if ($user && in_array($user->role?->name, ['super_admin', 'reviewer'], true)) {
            return null;
        }

        if ($user && $document->user_id === $user->id) {
            return null;
        }

        if ($document->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Document preview is not available before approval.',
            ], 403);
        }

        if ($document->license && $document->license->license_name === 'All Rights Reserved') {
            return response()->json([
                'success' => false,
                'message' => 'Preview is restricted for this document license.',
            ], 403);
        }

        if ($document->access_right === 'private') {
            return response()->json([
                'success' => false,
                'message' => 'Preview is not available for private documents.',
            ], 403);
        }

        if ($document->access_right === 'embargo' && $document->embargo_until && Carbon::parse($document->embargo_until)->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => 'Document is under embargo and preview is currently unavailable.',
            ], 403);
        }

        return null;
    }

    private function checkDownloadAccess(Document $document, User $user): ?JsonResponse
    {
        if (in_array($user->role?->name, ['super_admin', 'reviewer'], true)) {
            return null;
        }

        if ($document->user_id === $user->id) {
            return null;
        }

        return response()->json([
            'success' => false,
            'message' => 'Direct download is restricted. Please submit a download request to the document owner.',
        ], 403);
    }

    private function trackPreviewEvent(Document $document, ?User $user): void
    {
        Log::info('Document preview served', [
            'document_id' => $document->id,
            'user_id' => $user?->id,
        ]);
    }

    private function trackDownloadEvent(Document $document, User $user): void
    {
        Log::info('Document download served', [
            'document_id' => $document->id,
            'user_id' => $user->id,
        ]);
    }

    private function resolveStoragePath(string $relativePath): ?string
    {
        if (Storage::disk('local')->exists($relativePath)) {
            return Storage::disk('local')->path($relativePath);
        }

        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->path($relativePath);
        }

        return null;
    }
}
