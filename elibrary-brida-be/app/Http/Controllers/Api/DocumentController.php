<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class DocumentController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = Document::query();

        $query->with(['type', 'subject', 'authors', 'license']);

        $query->where('status', 'approved');

        $user = auth('sanctum')->user();
        if (!$user || !in_array($user->role?->name, ['admin', 'super_admin', 'reviewer'])) {
            $query->where('access_right', '!=', 'private');
            $query->whereDoesntHave('license', function ($q) {
                $q->where('license_name', 'All Rights Reserved');
            });
        }

        if ($request->filled('q') || $request->filled('search')) {
            $search = $request->input('q') ?: $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
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
            $query->whereIn('subject_id', (array) $request->subject_id);
        }

        if ($request->filled('license_id')) {
            $query->where('license_id', $request->license_id);
        }

        return response()->json($query->paginate(10));


    }

    public function featuredContent()
    {
        $restrictedLicenses = function ($q) {
            $q->where('license_name', 'All Rights Reserved');
        };

        return response()->json([
            'featured' => Document::where('is_featured', true)
                ->where('status', 'approved')
                ->where('access_right', '!=', 'private')
                ->whereDoesntHave('license', $restrictedLicenses)
                ->orderBy('upload_date', 'desc')
                ->limit(10)
                ->get(),
            'latest' => Document::where('status', 'approved')
                ->where('access_right', '!=', 'private')
                ->whereDoesntHave('license', $restrictedLicenses)
                ->orderBy('upload_date', 'desc')
                ->limit(10)
                ->get(),
            'most_downloaded' => Document::where('status', 'approved')
                ->where('access_right', '!=', 'private')
                ->whereDoesntHave('license', $restrictedLicenses)
                ->orderBy('download_count', 'desc')
                ->limit(10)
                ->get(),
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate([
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

            'subject_id' => 'nullable|exists:subjects,id',
            'access_right' => ['required', Rule::in(['open','public','internal','private','embargo'])],

            'embargo_until' => 'required_if:access_right,embargo|date|after:today',

            'statement_agreed' => 'required|accepted',
        ]);

        return response()->json([
            'success' => true,
            'preview' => $request->all()
        ]);
    }

    public function upload(Request $request)
    {
        // DEBUG: Log incoming request
        Log::info('=== Document Upload Request ===');
        Log::info('Content-Type: ' . $request->header('Content-Type'));
        Log::info('Has file: ' . ($request->hasFile('file') ? 'YES' : 'NO'));

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            Log::info('File details:', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'error' => $file->getError()
            ]);
        } else {
            Log::error('File field content:', [
                'type' => gettype($request->input('file')),
                'value' => $request->input('file')
            ]);
        }

        $request->validate([
            'title' => 'required|string|max:255',

            'year_published' => 'nullable|integer',
            'type_id' => 'nullable|exists:types,id',
            'unit_id' => 'nullable|exists:units,id',
            'language' => 'nullable|string',
            'email' => 'nullable|email',
            'keywords' => 'nullable|string',

            'abstract_id' => 'nullable|string',
            'abstract_en' => 'nullable|string',
            'publisher' => 'nullable|string|max:255',

            'license_id' => 'nullable|exists:licenses,id',

            'access_right' => ['nullable', Rule::in(['open','public','internal','private','embargo'])],
            'embargo_until' => 'required_if:access_right,embargo|date|after:today',

            'statement_agreed' => 'nullable',  // Make it optional for wizard mode

            'file' => 'required|file|mimes:pdf,doc,docx|max:50000',

            'authors' => 'required|array|min:1',
            'authors.*.first_name' => 'required|string',
            'authors.*.last_name' => 'nullable|string',
            'authors.*.email' => 'nullable|email',
            'authors.*.institution' => 'nullable|string',

            'supervisors' => 'nullable|array',
            'supervisors.*.name' => 'nullable|string',

            'subject_id' => 'nullable|exists:subjects,id',

            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:20000',
        ]);

        $mainPath = $request->file('file')->store('documents/main');

        $document = Document::create([
            'user_id' => auth('sanctum')->id(),
            'title' => $request->title,
            'year_published' => $request->year_published,
            'type_id' => $request->type_id,
            'unit_id' => $request->unit_id,
            'subject_id' => $request->subject_id,
            'language' => $request->language,
            'email' => $request->email,
            'keywords' => $request->keywords,

            'abstract_id' => $request->abstract_id,
            'abstract_en' => $request->abstract_en,
            'publisher' => $request->publisher,

            'file_path' => $mainPath,
            'upload_date' => now(),

            'license_id' => $request->license_id,
            'access_right' => $request->access_right,
            'embargo_until' => $request->embargo_until,

            'statement_agreed' => $request->has('statement_agreed') ? true : false,

            'status' => 'pending'
        ]);

        Log::info('Document created successfully', [
            'document_id' => $document->id,
            'title' => $document->title,
            'status' => $document->status,
            'user_id' => $document->user_id
        ]);

        foreach ($request->authors as $a) {
            $document->authors()->create($a);
        }

        if ($request->supervisors) {
            foreach ($request->supervisors as $s) {
                $document->supervisors()->create($s);
            }
        }

        if ($request->attachments) {
            foreach ($request->attachments as $file) {
                $path = $file->store('documents/attachments');

                $document->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dikirim & menunggu persetujuan admin.',
            'data' => [
                'id' => $document->id,
                'title' => $document->title,
                'status' => $document->status,
                'created_at' => $document->created_at->toISOString()
            ]
        ]);
    }


    public function myDocuments()
    {
        return response()->json([
            'success' => true,
            'documents' => Document::with(['authors', 'supervisors', 'subject'])
                ->where('user_id', auth('sanctum')->id())
                ->orderBy('upload_date', 'desc')
                ->get()
        ]);
    }

    public function index(Request $request)
    {
        /** @var User|null $user */
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $user = User::with('roles')->find($user->id);

        Log::info('Document index access attempt', [
            'user_id' => $user->id,
            'role' => $user->role?->name ?? 'no role'
        ]);

        $query = Document::with(['user', 'type']);

        if ($user->role?->name === 'contributor') {
            $query->where('user_id', $user->id);
        }

        elseif ($user->role?->name === 'guest') {
            $query->where('status', 'approved')
                ->where('access_right', '!=', 'private')
                ->whereDoesntHave('license', function ($q) {
                    $q->where('license_name', 'All Rights Reserved');
                });
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function review()
    {
        $documents = Document::with(['user', 'type'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function getReviewHistory()
    {
        try {
            Log::info("getReviewHistory called");

            // First check total review count
            $totalReviews = Review::count();
            Log::info("Total reviews in database: {$totalReviews}");

            // Get all reviews (before filtering)
            $allReviews = Review::with(['document', 'document.user', 'user'])->get();
            Log::info("Reviews with joined data: " . count($allReviews));

            // Now apply whereHas filter
            $reviews = Review::with(['document', 'document.user', 'user'])
                ->whereHas('document') // Only include reviews for documents that still exist
                ->orderBy('review_date', 'desc')
                ->get();

            Log::info("getReviewHistory: Found " . count($reviews) . " reviews after filtering");

            // Log each review
            foreach ($reviews as $review) {
                Log::info("Review ID: {$review->id}, Doc ID: {$review->document_id}, Status: {$review->status_review}, Date: {$review->review_date}");
            }

            $mappedReviews = $reviews->map(function ($review) {
                Log::info("Mapping review {$review->id}: document={$review->document?->title}");
                return [
                    'id' => $review->id, // review record ID for deletion
                    'document_id' => $review->document_id,
                    'name' => $review->document->user?->name ?? 'Unknown',
                    'email' => $review->document->user?->email ?? '',
                    'title' => $review->document->title,
                    'status' => $review->status_review === 'approved' ? 'Accepted' : 'Rejected',
                    'lastUpdate' => [
                        'date' => Carbon::parse($review->review_date)->format('d/m/Y'),
                        'time' => Carbon::parse($review->review_date)->format('H:i') . ' WIB'
                    ],
                    'review_comment' => $review->comment,
                    'reviewer_name' => $review->user?->name ?? 'Unknown',
                ];
            });

            $result = $mappedReviews->values()->all();
            Log::info("Final result count: " . count($result));

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching review history: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat history review: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $filename, 'public');
        }

        $typeId = $this->mapCategoryToTypeId($validated['category'] ?? null);

        $document = Document::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'abstract_id' => $validated['description'] ?? null,
            'author' => $validated['author'],
            'publisher' => $validated['publisher'] ?? null,
            'year_published' => $validated['year'] ?? now()->year,
            'keywords' => $validated['keywords'] ?? null,
            'file_path' => $filePath,
            'status' => 'pending',
            'type_id' => $typeId,
            'access_right' => 'public',

            'language' => $validated['language'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'advisor' => $validated['advisor'] ?? null,
            'funding_program' => $validated['funding'] ?? null,
            'research_location' => $validated['research_location'] ?? null,
            'upload_date' => now(),
            'statement_agreed' => true,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $attachmentFile) {
                $attachmentFilename = time() . '_attachment_' . $index . '_' . $attachmentFile->getClientOriginalName();
                $attachmentPath = $attachmentFile->storeAs('documents/attachments', $attachmentFilename, 'public');

                $document->attachments()->create([
                    'file_path' => $attachmentPath,
                    'file_name' => $attachmentFile->getClientOriginalName(),
                    'file_type' => $attachmentFile->getClientOriginalExtension(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diunggah dan menunggu persetujuan admin',
            'data' => $document->load('attachments')
        ], 201);
    }


    private function mapCategoryToTypeId($category)
    {
        if (!$category) return null;

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

    public function show($id)
    {
        try {
            /** @var User|null $user */
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $document = Document::with([
                'user.roles',
                'type',
                'unit',
                'license',
                'authors',
                'supervisors',
                'subject',
                'attachments'
            ])->findOrFail($id);

            $userRole = $user->role?->name;

            Log::info('Document show access attempt', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'document_id' => $id,
                'document_owner_id' => $document->user_id,
                'document_status' => $document->status
            ]);


            if (!in_array($userRole, ['admin', 'super_admin', 'reviewer'])) {
                if ($document->user_id !== $user->id) {
                    // Any non-privileged user (guest, contributor, etc.) — apply public access rules
                    if ($document->status !== 'approved') {
                        return response()->json([
                            'success' => false,
                            'message' => 'This document is not publicly available yet'
                        ], 403);
                    }
                    if ($document->access_right === 'private') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Dokumen ini bersifat privat dan tidak dapat diakses'
                        ], 403);
                    }
                    if ($document->license && $document->license->license_name === 'All Rights Reserved') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Akses ke dokumen ini dibatasi berdasarkan lisensi All Rights Reserved'
                        ], 403);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $document
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching document', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching document: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:pending,approved,rejected',
            'admin_notes' => 'sometimes|nullable|string',
            'category' => 'sometimes|string',
            'year' => 'sometimes|integer',
            'author' => 'sometimes|string|max:255',
            'publisher' => 'sometimes|string|max:255',
            'keywords' => 'sometimes|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $validated['file_path'] = $file->storeAs('documents', $filename, 'public');
        }

        if (isset($validated['description'])) {
            $validated['abstract'] = $validated['description'];
            unset($validated['description']);
        }

        if (isset($validated['year'])) {
            $validated['year_published'] = $validated['year'];
            unset($validated['year']);
        }

        $oldStatus = $document->status;
        $document->update($validated);

        // Otomatis buat/update review record jika status berubah ke approved/rejected
        if (isset($validated['status']) && in_array($validated['status'], ['approved', 'rejected']) && $oldStatus !== $validated['status']) {
            /** @var User|null $user */
            $user = auth('sanctum')->user();
            Review::updateOrCreate(
                ['document_id' => $document->id],
                [
                    'user_id' => $user?->id,
                    'status_review' => $validated['status'],
                    'comment' => $request->admin_notes ?? null,
                    'review_date' => now(),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diperbarui',
            'data' => $document
        ]);
    }

    public function destroy($id)
    {
        try {
            /** @var User|null $user */
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Check if ID refers to a review (history delete) or document (document delete)
            $review = Review::find($id);

            if ($review) {
                // Case 1: Delete review record (history delete)
                $documentTitle = $review->document?->title ?? 'Unknown Document';
                $review->delete();

                Log::info("Review record {$id} deleted by user {$user->id} for document: {$documentTitle}");

                return response()->json([
                    'success' => true,
                    'message' => 'History berhasil dihapus'
                ]);
            } else {
                // Case 2: Delete document (contributor deleting their own document)
                $document = Document::findOrFail($id);

                // Authorization: only contributor who uploaded or admin can delete
                if ($user->id !== $document->user_id && !in_array($user->role?->name, ['admin', 'super_admin'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk menghapus dokumen ini'
                    ], 403);
                }

                // Delete associated file
                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }

                // Delete associated reviews
                Review::where('document_id', $document->id)->delete();

                // Delete the document
                $document->delete();

                Log::info("Document {$id} ('{$document->title}') deleted by user {$user->id}");

                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil dihapus'
                ]);
            }

            // Delete attachment files before deleting records
            foreach ($document->attachments as $attachment) {
                if ($attachment->file_path) {
                    // Try 'local' disk first
                    if (Storage::disk('local')->exists($attachment->file_path)) {
                        Storage::disk('local')->delete($attachment->file_path);
                        Log::info("Deleted attachment from local disk: {$attachment->file_path}");
                    }
                    // Fallback to 'public' disk
                    elseif (Storage::disk('public')->exists($attachment->file_path)) {
                        Storage::disk('public')->delete($attachment->file_path);
                        Log::info("Deleted attachment from public disk: {$attachment->file_path}");
                    } else {
                        Log::warning("Attachment file not found in any disk: {$attachment->file_path}");
                    }
                }
            }

            // Delete related database records
            $document->authors()->delete();
            $document->supervisors()->delete();
            $document->attachments()->delete();

            // Delete the document itself
            $document->delete();

            Log::info("Document {$id} successfully deleted by user {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error("Error deleting resource {$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }


    public function serveFile(Request $request, $id)
    {
        try {
            $token = $request->query('token');
            if ($token) {
                $request->headers->set('Authorization', 'Bearer ' . $token);
            }

            /** @var User|null $user */
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $document = Document::with('user.roles', 'license')->findOrFail($id);

            $accessCheck = $this->checkFileAccess($document, $user);
            if ($accessCheck) return $accessCheck;

            if (!$document->file_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            // Try to find file in different disks (for backward compatibility)
            $disk = 'local'; // default: storage/app/private
            $filePath = null;
            $mimeType = null;

            // First try the default 'local' disk (private storage)
            if (Storage::disk('local')->exists($document->file_path)) {
                $disk = 'local';
                $filePath = Storage::disk('local')->path($document->file_path);
                $mimeType = mime_content_type($filePath);
            }
            // Fallback to 'public' disk for old documents
            elseif (Storage::disk('public')->exists($document->file_path)) {
                $disk = 'public';
                $filePath = Storage::disk('public')->path($document->file_path);
                $mimeType = mime_content_type($filePath);
            }

            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di storage'
                ], 404);
            }

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($document->file_path) . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error serving file', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error serving file: ' . $e->getMessage()
            ], 500);
        }
    }


    public function serveAttachment(Request $request, $documentId, $attachmentId)
    {
        try {
            $token = $request->query('token');
            if ($token) {
                $request->headers->set('Authorization', 'Bearer ' . $token);
            }

            /** @var User|null $user */
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $attachment = DocumentAttachment::where('document_id', $documentId)
                ->where('id', $attachmentId)
                ->firstOrFail();

            $document = Document::with('user.roles', 'license')->findOrFail($documentId);

            $accessCheck = $this->checkFileAccess($document, $user);
            if ($accessCheck) return $accessCheck;

            if (!$attachment->file_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            // Try to find file in different disks (for backward compatibility)
            $disk = 'local'; // default: storage/app/private
            $filePath = null;
            $mimeType = null;

            // First try the default 'local' disk (private storage)
            if (Storage::disk('local')->exists($attachment->file_path)) {
                $disk = 'local';
                $filePath = Storage::disk('local')->path($attachment->file_path);
                $mimeType = mime_content_type($filePath);
            }
            // Fallback to 'public' disk for old attachments
            elseif (Storage::disk('public')->exists($attachment->file_path)) {
                $disk = 'public';
                $filePath = Storage::disk('public')->path($attachment->file_path);
                $mimeType = mime_content_type($filePath);
            }

            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di storage'
                ], 404);
            }

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $attachment->file_name . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error serving attachment', [
                'documentId' => $documentId,
                'attachmentId' => $attachmentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error serving attachment: ' . $e->getMessage()
            ], 500);
        }
    }

    private function checkFileAccess(Document $document, User $user): ?JsonResponse
    {
        $role = $user->role?->name;

        if (in_array($role, ['admin', 'super_admin', 'reviewer'])) {
            return null;
        }

        if ($document->user_id === $user->id) {
            return null;
        }

        if ($document->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Dokumen belum dipublikasikan'], 403);
        }

        if ($document->license && $document->license->license_name === 'All Rights Reserved') {
            return response()->json(['success' => false, 'message' => 'File ini tidak dapat diunduh langsung karena dilindungi lisensi All Rights Reserved. Ajukan permohonan download.'], 403);
        }

        switch ($document->access_right) {
            case 'open':
            case 'public':
                return null;

            case 'internal':
                if ($role === 'guest') {
                    return response()->json(['success' => false, 'message' => 'Dokumen ini hanya untuk pengguna internal'], 403);
                }
                return null;

            case 'private':
                return response()->json(['success' => false, 'message' => 'Dokumen ini bersifat privat'], 403);

            case 'embargo':
                if ($document->embargo_until && Carbon::parse($document->embargo_until)->isFuture()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dokumen masih dalam masa embargo hingga ' . Carbon::parse($document->embargo_until)->format('d M Y')
                    ], 403);
                }
                return null;

            default:
                return null;
        }
    }
}
