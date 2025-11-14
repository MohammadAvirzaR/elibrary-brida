<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;

class DocumentController extends Controller
{
    public function search(Request $request)
    {
        $query = Document::query();

        // Terima parameter 'q' atau 'search' untuk pencarian teks
        if ($request->filled('q') || $request->filled('search')) {
            $search = $request->input('q') ?: $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                ->orWhere('author', 'like', "%$search%")
                ->orWhere('keywords', 'like', "%$search%")
                ->orWhere('abstract', 'like', "%$search%");
            });
        }

            // Filter type
            if ($request->filled('type_id')) {
                $query->whereIn('type_id', (array) $request->type_id);
            }

            // Filter year range
            if ($request->filled('year')) {
                $year = now()->year - $request->year;
                $query->where('year_published', '>=', $year);
            }

            // Filter access rights
            if ($request->filled('access_right')) {
                $query->where('access_right', $request->access_right);
            }

            // ✅ Filter subject via pivot table
            if ($request->filled('subject_id')) {
                $query->whereHas('subjects', function ($q) use ($request) {
                    $q->whereIn('subjects.id', (array) $request->subject_id);
                });
            }

            return $query->paginate(10);
    }

    public function featuredContent()
    {
        $featured = Document::where('is_featured', true)
            ->orderBy('upload_date', 'desc')
            ->limit(10)
            ->get();

        $latest = Document::orderBy('upload_date', 'desc')
            ->limit(10)
            ->get();

        $mostDownloaded = Document::orderBy('download_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'featured' => $featured,
            'latest' => $latest,
            'most_downloaded' => $mostDownloaded,
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Document::with(['user', 'type']);

        if ($user->role->name === 'contributor') {
            $query->where('user_id', $user->id);
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

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|string',
            'year' => 'required|integer',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'keywords' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $filename, 'public');
        }

        $document = Document::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'abstract' => $validated['description'],
            'author' => $validated['author'],
            'publisher' => $validated['publisher'] ?? null,
            'year_published' => $validated['year'],
            'keywords' => $validated['keywords'] ?? null,
            'file_path' => $filePath,
            'status' => 'pending',
            'type_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diunggah',
            'data' => $document->load('user')
        ], 201);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|string',
            'year' => 'required|integer',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'keywords' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $filename, 'public');
        }

        $document = Document::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'abstract' => $validated['description'],
            'author' => $validated['author'],
            'publisher' => $validated['publisher'] ?? null,
            'year_published' => $validated['year'],
            'keywords' => $validated['keywords'] ?? null,
            'file_path' => $filePath,
            'status' => 'approved',
            'type_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dibuat',
            'data' => $document
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:pending,approved,rejected',
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
        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dihapus'
        ]);
    }
}
