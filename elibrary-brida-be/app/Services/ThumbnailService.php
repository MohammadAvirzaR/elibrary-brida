<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ThumbnailService
{
    /**
     * Generate thumbnail from PDF file
     *
     * @param string $pdfPath Full path to PDF file in storage
     * @param string $documentId Document ID for unique naming
     * @return string|null Path to generated thumbnail or null if failed
     */
    public function generateThumbnail(string $pdfPath, string $documentId): ?string
    {
        try {
            // TODO: When Imagick or GD with PDF support is available, implement actual thumbnail generation
            // For now, we'll use a fallback placeholder approach

            // Check if PDF file exists
            if (!Storage::exists($pdfPath)) {
                Log::warning("PDF file not found for thumbnail generation: {$pdfPath}");
                return null;
            }

            // Future implementation placeholder:
            // 1. Use Imagick or spatie/pdf-to-image to convert first page of PDF to image
            // 2. Resize to thumbnail dimensions (e.g., 300x400)
            // 3. Save to public/thumbnails directory
            // 4. Return the path

            // Example code (commented out until dependencies are available):
            /*
            $pdf = new \Spatie\PdfToImage\Pdf($pdfPath);
            $thumbnailPath = 'thumbnails/' . $documentId . '.jpg';
            $pdf->setPage(1)
                ->saveImage(storage_path('app/public/' . $thumbnailPath));
            return $thumbnailPath;
            */

            // For now, return null to use fallback in frontend
            Log::info("Thumbnail generation skipped (PDF libraries not available): {$documentId}");
            return null;

        } catch (\Exception $e) {
            Log::error("Failed to generate thumbnail for document {$documentId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete thumbnail file if exists
     *
     * @param string|null $thumbnailPath
     * @return bool
     */
    public function deleteThumbnail(?string $thumbnailPath): bool
    {
        if (!$thumbnailPath) {
            return false;
        }

        try {
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
                return true;
            }
        } catch (\Exception $e) {
            Log::error("Failed to delete thumbnail {$thumbnailPath}: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Get full URL for thumbnail
     *
     * @param string|null $thumbnailPath
     * @return string|null
     */
    public function getThumbnailUrl(?string $thumbnailPath): ?string
    {
        if (!$thumbnailPath) {
            return null;
        }

        return Storage::url($thumbnailPath);
    }
}
