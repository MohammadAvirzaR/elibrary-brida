<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\User;
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

        // Only show approved documents on public search/landing page
        $query->where('status', 'approved');

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
        return response()->json($query->paginate(10));


    }

    public function featuredContent()
    {
        return response()->json([
            'featured' => Document::where('is_featured', true)
                ->where('status', 'approved')
                ->orderBy('upload_date', 'desc')
                ->limit(10)
                ->get(),
            'latest' => Document::where('status', 'approved')
                ->orderBy('upload_date', 'desc')
                ->limit(10)
                ->get(),
            'most_downloaded' => Document::where('status', 'approved')
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

            'subjects' => 'array',
            'access_right' => ['required', Rule::in(['public','internal','embargo'])],

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

            'funding_program' => 'nullable|string',
            'research_location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            'license_id' => 'nullable|exists:licenses,id',

            'access_right' => ['nullable', Rule::in(['public','internal','embargo'])],
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

            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',

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
            'language' => $request->language,
            'email' => $request->email,
            'keywords' => $request->keywords,

            'abstract_id' => $request->abstract_id,
            'abstract_en' => $request->abstract_en,

            'file_path' => $mainPath,
            'upload_date' => now(),

            'license_id' => $request->license_id,
            'access_right' => $request->access_right,
            'embargo_until' => $request->embargo_until,

            'funding_program' => $request->funding_program,
            'research_location' => $request->research_location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,

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

        if ($request->subjects) {
            $document->subjects()->sync($request->subjects);
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
            'documents' => Document::with(['authors', 'supervisors', 'subjects'])
                ->where('user_id', auth('sanctum')->id())
                ->orderBy('upload_date', 'desc')
                ->get()
        ]);
    }

    public function index(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $user = User::with('role')->find($user->id);

        Log::info('Document index access attempt', [
            'user_id' => $user->id,
            'role' => $user->role ? $user->role->name : 'no role'
        ]);

        $query = Document::with(['user', 'type']);

        if ($user->role && $user->role->name === 'contributor') {
            $query->where('user_id', $user->id);
        }

        elseif ($user->role && $user->role->name === 'guest') {
            $query->where('status', 'approved');
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
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $document = Document::with([
                'user.role',
                'type',
                'unit',
                'license',
                'authors',
                'supervisors',
                'subjects',
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
                if ($document->user_id === $user->id) {
                } else if ($userRole === 'guest') {
                    if ($document->status !== 'approved') {
                        return response()->json([
                            'success' => false,
                            'message' => 'This document is not publicly available yet'
                        ], 403);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to view this document'
                    ], 403);
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

        $document->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diperbarui',
            'data' => $document
        ]);
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        $user = auth('sanctum')->user();
        if ($user->role->name === 'contributor' && $document->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus dokumen ini'
            ], 403);
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Delete related data
        $document->authors()->delete();
        $document->supervisors()->delete();
        $document->attachments()->delete();
        $document->subjects()->detach();

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dihapus'
        ]);
    }


    public function serveFile(Request $request, $id)
    {
        try {
            $token = $request->query('token');
            if ($token) {
                $request->headers->set('Authorization', 'Bearer ' . $token);
            }

            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $document = Document::with('user.role')->findOrFail($id);

            $userRole = $user->role?->name;

            if (!in_array($userRole, ['admin', 'super_admin', 'reviewer'])) {
                if ($document->user_id === $user->id) {
                } else if ($userRole === 'guest') {
                    if ($document->status !== 'approved') {
                        return response()->json([
                            'success' => false,
                            'message' => 'This document is not publicly available yet'
                        ], 403);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to access this document'
                    ], 403);
                }
            }

            if (!$document->file_path || !Storage::exists($document->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            $filePath = Storage::path($document->file_path);
            $mimeType = Storage::mimeType($document->file_path);

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

            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }            $attachment = DocumentAttachment::where('document_id', $documentId)
                ->where('id', $attachmentId)
                ->firstOrFail();

            $document = Document::with('user.role')->findOrFail($documentId);

            $userRole = $user->role?->name;

            if (!in_array($userRole, ['admin', 'super_admin', 'reviewer'])) {
                if ($document->user_id === $user->id) {
                } else if ($userRole === 'guest') {
                    if ($document->status !== 'approved') {
                        return response()->json([
                            'success' => false,
                            'message' => 'This document is not publicly available yet'
                        ], 403);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to access this document'
                    ], 403);
                }
            }

            if (!$attachment->file_path || !Storage::exists($attachment->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            $filePath = Storage::path($attachment->file_path);
            $mimeType = Storage::mimeType($attachment->file_path);

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
}
