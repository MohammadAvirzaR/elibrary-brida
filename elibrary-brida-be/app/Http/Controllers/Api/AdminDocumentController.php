<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;

class AdminDocumentController extends Controller
{
    public function approve($id)
    {
        $doc = Document::findOrFail($id);
        $doc->status = 'approved';
        $doc->save();

        return response()->json(['success' => true, 'message' => 'Document approved.']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['note' => 'nullable|string']);
        $doc = Document::findOrFail($id);
        $doc->status = 'rejected';
        $doc->review_notes = $request->note ?? null;
        $doc->save();

        return response()->json(['success' => true, 'message' => 'Document rejected.']);
    }
}
