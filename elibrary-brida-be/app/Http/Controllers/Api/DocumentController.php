<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    private const APPROVED_STATUS = 'approved';
    private const PENDING_STATUS = 'pending';
    private const MAX_FILE_SIZE = 50000;
    private const ITEMS_PER_PAGE = 10;
    private const PRIVILEGED_ROLES = ['admin', 'super_admin', 'reviewer'];

    public function search(Request $request): JsonResponse
    {
        $query = Document::where('status', self::APPROVED_STATUS);

        $this->applySearchFilters($query, $request);

        return response()->json($query->paginate(self::ITEMS_PER_PAGE));
    }

    public function featuredContent(): JsonResponse
    {
        return response()->json([
            'featured' => $this->getApprovedDocuments('is_featured', true, 'upload_date'),
            'latest' => $this->getApprovedDocuments(null, null, 'upload_date'),
            'most_downloaded' => $this->getApprovedDocuments(null, null, 'download_count'),
        ]);
    }

    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate($this->getPreviewValidationRules());

        return response()->json([
            'success' => true,
            'preview' => $validated
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate($this->getUploadValidationRules());

        try {
            DB::beginTransaction();

            $document = $this->createDocument($request, $validated);
            $this->attachRelatedData($document, $request);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dikirim & menunggu persetujuan admin.',
                'data' => $this->formatDocumentResponse($document)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document upload failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah dokumen'
            ], 500);
        }
    }

    public function myDocuments(): JsonResponse
    {
        $documents = Document::with(['authors', 'supervisors', 'subjects'])
            ->where('user_id', auth('sanctum')->id())
            ->orderBy('upload_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }

    public function index(): JsonResponse
    {
        $user = $this->getAuthenticatedUserWithRole();

        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $query = Document::with(['user', 'type']);
        $this->applyRoleBasedFilter($query, $user);

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('created_at', 'desc')->get()
        ]);
    }

    public function review(): JsonResponse
    {
        $documents = Document::with(['user', 'type'])
            ->where('status', self::PENDING_STATUS)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->getStoreValidationRules());

        try {
            DB::beginTransaction();

            $document = $this->createSimpleDocument($request, $validated);
            $this->handleAttachments($document, $request);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diunggah dan menunggu persetujuan admin',
                'data' => $document->load('attachments')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document store failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan dokumen'
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return $this->unauthorizedResponse();
            }

            $document = Document::with([
                'user.role', 'type', 'unit', 'license',
                'authors', 'supervisors', 'subjects', 'attachments'
            ])->findOrFail($id);

            if (!$this->canViewDocument($user, $document)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this document'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $document
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching document', ['id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching document'
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            $validated = $request->validate($this->getUpdateValidationRules());

            $this->handleFileUpdate($request, $validated);
            $this->normalizeValidatedData($validated);

            $document->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui',
                'data' => $document
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            $user = auth('sanctum')->user();

            if (!$this->canDeleteDocument($user, $document)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus dokumen ini'
                ], 403);
            }

            $this->deleteDocumentAndFiles($document);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        }
    }

    public function serveFile(Request $request, string $id): BinaryFileResponse|JsonResponse
    {
        try {
            $user = $this->authenticateFromRequest($request);

            if (!$user) {
                return $this->unauthorizedResponse();
            }

            $document = Document::with('user.role')->findOrFail($id);

            if (!$this->canAccessFile($user, $document)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this document'
                ], 403);
            }

            if (!$document->file_path || !Storage::exists($document->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            return response()->file(
                Storage::path($document->file_path),
                [
                    'Content-Type' => Storage::mimeType($document->file_path),
                    'Content-Disposition' => 'inline; filename="' . basename($document->file_path) . '"'
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error serving file', ['id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error serving file'
            ], 500);
        }
    }

    public function serveAttachment(Request $request, string $documentId, string $attachmentId): BinaryFileResponse|JsonResponse
    {
        try {
            $user = $this->authenticateFromRequest($request);

            if (!$user) {
                return $this->unauthorizedResponse();
            }

            $attachment = DocumentAttachment::where('document_id', $documentId)
                ->where('id', $attachmentId)
                ->firstOrFail();

            $document = Document::with('user.role')->findOrFail($documentId);

            if (!$this->canAccessFile($user, $document)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this document'
                ], 403);
            }

            if (!$attachment->file_path || !Storage::exists($attachment->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            return response()->file(
                Storage::path($attachment->file_path),
                [
                    'Content-Type' => Storage::mimeType($attachment->file_path),
                    'Content-Disposition' => 'inline; filename="' . $attachment->file_name . '"'
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error serving attachment', [
                'documentId' => $documentId,
                'attachmentId' => $attachmentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error serving attachment'
            ], 500);
        }
    }

    private function applySearchFilters($query, Request $request): void
    {
        if ($request->filled('q') || $request->filled('search')) {
            $search = $request->input('q') ?: $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('keywords', 'like', "%$search%")
                    ->orWhere('abstract_id', 'like', "%$search%")
                    ->orWhere('abstract_en', 'like', "%$search%");
            });
        }

        if ($request->filled('type_id')) {
            $query->whereIn('type_id', (array) $request->type_id);
        }

        if ($request->filled('year')) {
            $year = now()->year - $request->year;
            $query->where('year_published', '>=', $year);
        }

        if ($request->filled('access_right')) {
            $query->where('access_right', $request->access_right);
        }

        if ($request->filled('subject_id')) {
            $query->whereHas('subjects', function ($q) use ($request) {
                $q->whereIn('subjects.id', (array) $request->subject_id);
            });
        }
    }

    private function getApprovedDocuments(?string $column = null, $value = null, string $orderBy = 'upload_date')
    {
        $query = Document::where('status', self::APPROVED_STATUS);

        if ($column && $value !== null) {
            $query->where($column, $value);
        }

        return $query->orderBy($orderBy, 'desc')->limit(10)->get();
    }

    private function getPreviewValidationRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'authors' => 'required|array|min:1',
            'authors.*.first_name' => 'required|string',
            'authors.*.last_name' => 'nullable|string',
            'authors.*.email' => 'nullable|email',
            'authors.*.institution' => 'nullable|string',
            'supervisors' => 'array',
            'year_published' => 'nullable|integer',
            'language' => 'nullable|string',
            'keywords' => 'nullable|string',
            'abstract_id' => 'nullable|string',
            'abstract_en' => 'nullable|string',
            'funding_program' => 'nullable|string',
            'research_location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'subjects' => 'array',
            'access_right' => ['required', Rule::in(['public', 'internal', 'embargo'])],
            'embargo_until' => 'required_if:access_right,embargo|date|after:today',
            'statement_agreed' => 'required|accepted',
        ];
    }

    private function getUploadValidationRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'year_published' => 'nullable|integer',
            'type_id' => 'nullable|exists:types,id',
            'unit_id' => 'nullable|exists:units,id',
            'language' => 'nullable|string',
            'email' => 'nullable|email',
            'keywords' => 'nullable|string',
            'abstract_id' => 'nullable|string',
            'abstract_en' => 'nullable|string',
            'funding_program' => 'nullable|string',
            'research_location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'license_id' => 'nullable|exists:licenses,id',
            'access_right' => ['nullable', Rule::in(['public', 'internal', 'embargo'])],
            'embargo_until' => 'required_if:access_right,embargo|date|after:today',
            'statement_agreed' => 'nullable',
            'file' => 'required|file|mimes:pdf,doc,docx|max:' . self::MAX_FILE_SIZE,
            'authors' => 'required|array|min:1',
            'authors.*.first_name' => 'required|string',
            'authors.*.last_name' => 'nullable|string',
            'authors.*.email' => 'nullable|email',
            'authors.*.institution' => 'nullable|string',
            'supervisors' => 'nullable|array',
            'supervisors.*.name' => 'nullable|string',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:20000',
        ];
    }

    private function getStoreValidationRules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'year' => 'nullable|integer',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'keywords' => 'nullable|string',
            'language' => 'nullable|string',
            'subject' => 'nullable|string',
            'advisor' => 'nullable|string',
            'funding' => 'nullable|string',
            'research_location' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf|max:10240',
        ];
    }

    private function getUpdateValidationRules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:pending,approved,rejected',
            'admin_notes' => 'sometimes|nullable|string',
            'category' => 'sometimes|string',
            'year' => 'sometimes|integer',
            'author' => 'sometimes|string|max:255',
            'publisher' => 'sometimes|string|max:255',
            'keywords' => 'sometimes|string',
        ];
    }

    private function createDocument(Request $request, array $validated): Document
    {
        $mainPath = $request->file('file')->store('documents/main');

        return Document::create([
            'user_id' => auth('sanctum')->id(),
            'title' => $validated['title'],
            'year_published' => $validated['year_published'] ?? null,
            'type_id' => $validated['type_id'] ?? null,
            'unit_id' => $validated['unit_id'] ?? null,
            'language' => $validated['language'] ?? null,
            'email' => $validated['email'] ?? null,
            'keywords' => $validated['keywords'] ?? null,
            'abstract_id' => $validated['abstract_id'] ?? null,
            'abstract_en' => $validated['abstract_en'] ?? null,
            'file_path' => $mainPath,
            'upload_date' => now(),
            'license_id' => $validated['license_id'] ?? null,
            'access_right' => $validated['access_right'] ?? null,
            'embargo_until' => $validated['embargo_until'] ?? null,
            'funding_program' => $validated['funding_program'] ?? null,
            'research_location' => $validated['research_location'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'statement_agreed' => $request->has('statement_agreed'),
            'status' => self::PENDING_STATUS
        ]);
    }

    private function attachRelatedData(Document $document, Request $request): void
    {
        if ($request->authors) {
            foreach ($request->authors as $author) {
                $document->authors()->create($author);
            }
        }

        if ($request->supervisors) {
            foreach ($request->supervisors as $supervisor) {
                $document->supervisors()->create($supervisor);
            }
        }

        if ($request->subjects) {
            $document->subjects()->sync($request->subjects);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('documents/attachments');
                $document->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }
    }

    private function createSimpleDocument(Request $request, array $validated): Document
    {
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $filename, 'public');
        }

        return Document::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'abstract_id' => $validated['description'] ?? null,
            'author' => $validated['author'],
            'publisher' => $validated['publisher'] ?? null,
            'year_published' => $validated['year'] ?? now()->year,
            'keywords' => $validated['keywords'] ?? null,
            'file_path' => $filePath,
            'status' => self::PENDING_STATUS,
            'type_id' => $this->mapCategoryToTypeId($validated['category'] ?? null),
            'access_right' => 'public',
            'language' => $validated['language'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'advisor' => $validated['advisor'] ?? null,
            'funding_program' => $validated['funding'] ?? null,
            'research_location' => $validated['research_location'] ?? null,
            'upload_date' => now(),
            'statement_agreed' => true,
        ]);
    }

    private function handleAttachments(Document $document, Request $request): void
    {
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $attachmentFile) {
                $filename = time() . '_attachment_' . $index . '_' . $attachmentFile->getClientOriginalName();
                $path = $attachmentFile->storeAs('documents/attachments', $filename, 'public');

                $document->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $attachmentFile->getClientOriginalName(),
                    'file_type' => $attachmentFile->getClientOriginalExtension(),
                ]);
            }
        }
    }

    private function mapCategoryToTypeId(?string $category): ?int
    {
        if (!$category) {
            return null;
        }

        $categoryMap = [
            'penelitian' => 1,
            'laporan' => 2,
            'artikel' => 3,
            'jurnal' => 4,
            'skripsi' => 5,
            'buku' => 6,
            'lainnya' => 7,
        ];

        return $categoryMap[strtolower($category)] ?? null;
    }

    private function formatDocumentResponse(Document $document): array
    {
        return [
            'id' => $document->id,
            'title' => $document->title,
            'status' => $document->status,
            'created_at' => $document->created_at->toISOString()
        ];
    }

    private function getAuthenticatedUserWithRole(): ?User
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return null;
        }

        return User::with('role')->find($user->id);
    }

    private function applyRoleBasedFilter($query, User $user): void
    {
        $roleName = $user->role?->name;

        if ($roleName === 'contributor') {
            $query->where('user_id', $user->id);
        } elseif ($roleName === 'guest') {
            $query->where('status', self::APPROVED_STATUS);
        }
    }

    private function canViewDocument($user, Document $document): bool
    {
        $userRole = $user->role?->name;

        if (in_array($userRole, self::PRIVILEGED_ROLES)) {
            return true;
        }

        if ($document->user_id === $user->id) {
            return true;
        }

        if ($userRole === 'guest' && $document->status === self::APPROVED_STATUS) {
            return true;
        }

        return false;
    }

    private function canAccessFile($user, Document $document): bool
    {
        return $this->canViewDocument($user, $document);
    }

    private function canDeleteDocument($user, Document $document): bool
    {
        $roleName = $user->role?->name;

        if (in_array($roleName, self::PRIVILEGED_ROLES)) {
            return true;
        }

        return $roleName === 'contributor' && $document->user_id === $user->id;
    }

    private function deleteDocumentAndFiles(Document $document): void
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->authors()->delete();
        $document->supervisors()->delete();
        $document->attachments()->delete();
        $document->subjects()->detach();
        $document->delete();
    }

    private function handleFileUpdate(Request $request, array &$validated): void
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $validated['file_path'] = $file->storeAs('documents', $filename, 'public');
        }
    }

    private function normalizeValidatedData(array &$validated): void
    {
        if (isset($validated['description'])) {
            $validated['abstract'] = $validated['description'];
            unset($validated['description']);
        }

        if (isset($validated['year'])) {
            $validated['year_published'] = $validated['year'];
            unset($validated['year']);
        }
    }

    private function authenticateFromRequest(Request $request)
    {
        $token = $request->query('token');
        if ($token) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        return auth('sanctum')->user();
    }

    private function unauthorizedResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], 401);
    }
}
