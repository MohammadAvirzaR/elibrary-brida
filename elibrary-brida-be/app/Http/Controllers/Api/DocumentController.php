<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentAuthor;
use App\Models\DocumentSupervisor;
use App\Models\DocumentAttachment;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    // ============================================================
    // SEARCH
    // ============================================================
    public function search(Request $request)
    {
        $query = Document::query();

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

        return $query->paginate(10);
    }

    // ============================================================
    // FEATURED CONTENT
    // ============================================================
    public function featuredContent()
    {
        return response()->json([
            'featured' => Document::where('is_featured', true)->orderBy('upload_date', 'desc')->limit(10)->get(),
            'latest' => Document::orderBy('upload_date', 'desc')->limit(10)->get(),
            'most_downloaded' => Document::orderBy('download_count', 'desc')->limit(10)->get(),
        ]);
    }

    // ============================================================
    // PREVIEW (STEP 2)
    // ============================================================
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

            'statement_agreed' => 'required|boolean|accepted',
        ]);

        return response()->json([
            'success' => true,
            'preview' => $request->all()
        ]);
    }

    // ============================================================
    // FINAL SUBMIT (STEP 4)
    // ============================================================
    public function upload(Request $request)
    {
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

            'access_right' => ['required', Rule::in(['public','internal','embargo'])],
            'embargo_until' => 'required_if:access_right,embargo|date|after:today',

            'statement_agreed' => 'required|boolean|accepted',

            'file' => 'required|file|mimes:pdf,doc,docx|max:50000',

            'authors' => 'required|array|min:1',
            'authors.*.first_name' => 'required|string',

            'supervisors' => 'array',

            'subjects' => 'array',
            'subjects.*' => 'exists:subjects,id',

            'attachments.*' => 'file|max:20000',
        ]);

        // UPLOAD FILE UTAMA
        $mainPath = $request->file('file')->store('documents/main');

        // CREATE DOCUMENT
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

            'statement_agreed' => true,

            'status' => 'submitted'
        ]);

        // AUTHORS
        foreach ($request->authors as $a) {
            $document->authors()->create($a);
        }

        // SUPERVISORS
        if ($request->supervisors) {
            foreach ($request->supervisors as $s) {
                $document->supervisors()->create($s);
            }
        }

        // SUBJECTS
        if ($request->subjects) {
            $document->subjects()->sync($request->subjects);
        }

        // ATTACHMENTS
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
            'document_id' => $document->id
        ]);
    }

    // ============================================================
    // MY DOCUMENTS
    // ============================================================
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
}
