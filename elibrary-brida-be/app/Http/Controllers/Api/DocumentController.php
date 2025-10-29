<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;

class DocumentController extends Controller
{
    public function search(Request $request)
    {
           $keyword = $request->input('q');

    $documents = Document::search($keyword)
        ->where('status', 'approved') 
        ->paginate(10);

    return response()->json($documents);
    }
}
