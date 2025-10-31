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

            if ($request->filled('search')) {
                $search = $request->search;
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
}
