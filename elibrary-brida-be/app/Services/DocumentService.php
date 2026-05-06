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

        $generatedPreviewPath = $this->generatePreview($originalRelativePath, $previewRelativePath);

        return [
            'original_path' => $originalRelativePath,
            'preview_path' => $generatedPreviewPath,
        ];
    }

    public function generatePreview(string $originalRelativePath, ?string $previewRelativePath = null, ?int $maxPages = null): string
    {
        $this->ensurePdfPreviewDependencies();

        if (!Storage::disk('local')->exists($originalRelativePath)) {
            throw new RuntimeException('Original PDF file not found.');
        }

        $previewRelativePath ??= $this->buildDefaultPreviewPath($originalRelativePath);
        $maxPages ??= max((int) config('documents.preview_pages', 5), 1);

        $originalAbsolutePath = Storage::disk('local')->path($originalRelativePath);
        $previewAbsolutePath = Storage::disk('local')->path($previewRelativePath);
        $previewDirectory = dirname($previewRelativePath);

        Storage::disk('local')->makeDirectory($previewDirectory);

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($originalAbsolutePath);
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
}

