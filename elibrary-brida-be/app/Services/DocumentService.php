<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use setasign\Fpdi\Fpdi;

class DocumentService
{
    public function storeOriginalAndGeneratePreview(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'pdf';
        $baseName = Str::uuid()->toString();

        $originalRelativePath = "documents/original/{$baseName}.{$extension}";
        $previewRelativePath = "documents/preview/{$baseName}_preview.pdf";

        Storage::disk('local')->putFileAs('documents/original', $file, "{$baseName}.{$extension}");

        // Attempt to generate preview, but don't fail if it can't
        $generatedPreviewPath = $this->generatePreview($originalRelativePath, $previewRelativePath);

        return [
            'original_path' => $originalRelativePath,
            'preview_path' => $generatedPreviewPath,
        ];
    }

    public function generatePreview(string $originalRelativePath, ?string $previewRelativePath = null, ?int $maxPages = null): ?string
    {
        try {
            $this->ensurePdfPreviewDependencies();

            $sourceAbsolutePath = $this->resolveSourceAbsolutePath($originalRelativePath);
            if (!$sourceAbsolutePath) {
                throw new RuntimeException('Original PDF file not found.');
            }

            $previewRelativePath ??= $this->buildDefaultPreviewPath($originalRelativePath);
            $maxPages ??= max((int) config('documents.preview_pages', 5), 1);

            $previewAbsolutePath = Storage::disk('local')->path($previewRelativePath);
            $previewDirectory = dirname($previewRelativePath);

            Storage::disk('local')->makeDirectory($previewDirectory);

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($sourceAbsolutePath);
            $limit = min($pageCount, $maxPages);

            for ($page = 1; $page <= $limit; $page++) {
                $templateId = $pdf->importPage($page);
                $size = $pdf->getTemplateSize($templateId);
                $orientation = ($size['width'] ?? 0) > ($size['height'] ?? 0) ? 'L' : 'P';

                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }

            $pdf->Output('F', $previewAbsolutePath);

            return $previewRelativePath;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::warning('PDF preview generation failed', [
                'original_path' => $originalRelativePath,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            // Return null instead of throwing - allows document upload to proceed without preview
            return null;
        }
    }

    public function buildDefaultPreviewPath(string $originalRelativePath): string
    {
        $filename = pathinfo($originalRelativePath, PATHINFO_FILENAME);
        return "documents/preview/{$filename}_preview.pdf";
    }

    private function ensurePdfPreviewDependencies(): void
    {
        if (!class_exists(Fpdi::class)) {
            throw new RuntimeException(
                'FPDI dependency is missing. Install with: composer require setasign/fpdf setasign/fpdi'
            );
        }
    }

    private function resolveSourceAbsolutePath(string $relativePath): ?string
    {
        $candidates = array_values(array_unique(array_filter([
            $relativePath,
            ltrim($relativePath, '/'),
            preg_replace('#^storage/#', '', ltrim($relativePath, '/')),
            preg_replace('#^public/#', '', ltrim($relativePath, '/')),
        ])));

        foreach ($candidates as $candidate) {
            if (Storage::disk('local')->exists($candidate)) {
                return Storage::disk('local')->path($candidate);
            }

            if (Storage::disk('public')->exists($candidate)) {
                return Storage::disk('public')->path($candidate);
            }
        }

        return null;
    }
}
