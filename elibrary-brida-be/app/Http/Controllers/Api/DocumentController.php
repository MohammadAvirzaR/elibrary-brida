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

            if ($request->filled('type_id')) {
                $query->where('type_id', $request->type_id);
            }

            if ($request->filled('year')) {
                $query->where('year_published', $request->year);
            }
            
            if ($request->filled('access_right')) {
                $query->where('access_right', $request->access_right);
            }

            if ($request->filled('access_right')) {
                $query->where('access_right', $request->access_right);
            }

            return $query->paginate(10);
    }
}
