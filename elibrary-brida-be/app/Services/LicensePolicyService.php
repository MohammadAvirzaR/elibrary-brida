<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;

class LicensePolicyService
{
    public const ARR = 'ARR';
    public const CC_TYPES = ['CC_BY', 'CC_BY_SA', 'CC_BY_NC', 'CC_BY_ND'];

    public function canDownload(Document $document, ?User $user): bool
    {
        $role = $user?->role?->name;

        if (in_array($role, ['super_admin', 'reviewer'], true)) {
            return true;
        }

        if ($user && $document->user_id === $user->id) {
            return true;
        }

        if ($document->status !== 'approved') {
            return false;
        }

        if ($document->access_right === 'private') {
            return false;
        }

        if ($document->access_right === 'internal' && (!$user || $role === 'guest')) {
            return false;
        }

        if ($document->access_right === 'embargo' && $document->embargo_until && Carbon::parse($document->embargo_until)->isFuture()) {
            return false;
        }

        return $this->isCreativeCommons($document);
    }

    public function canEdit(Document $document, ?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if (in_array($user->role?->name, ['super_admin', 'reviewer'], true)) {
            return true;
        }

        return $document->user_id === $user->id;
    }

    public function requiresAttribution(Document $document): bool
    {
        return $this->isCreativeCommons($document);
    }

    public function generateAttribution(Document $document): string
    {
        $title = $document->title ?: 'Dokumen tanpa judul';
        $author = $document->user?->full_name ?? $document->user?->name ?? 'Penulis tidak diketahui';
        $version = $document->license_version ?: '4.0';
        $licenseType = $this->normalizeType($document->license_type);
        $label = str_replace('_', '-', $licenseType);

        if (!$this->isCreativeCommons($document)) {
            return "{$title} oleh {$author}. All Rights Reserved.";
        }

        return "{$title} oleh {$author} dilisensikan dengan {$label} {$version}.";
    }

    public function isCreativeCommons(Document $document): bool
    {
        return in_array($this->normalizeType($document->license_type), self::CC_TYPES, true);
    }

    public function isAllRightsReserved(Document $document): bool
    {
        return $this->normalizeType($document->license_type) === self::ARR;
    }

    private function normalizeType(?string $type): string
    {
        return strtoupper(trim((string) $type));
    }
}
