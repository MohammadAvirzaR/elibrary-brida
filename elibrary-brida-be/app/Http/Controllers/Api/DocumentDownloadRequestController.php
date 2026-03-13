<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DocumentDownloadMail;
use App\Models\Document;
use App\Models\DocumentDownloadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentDownloadRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'document_id'    => 'required|exists:documents,id',
            'name'           => 'required|string|max:255',
            'email'          => 'required|email',
            'institution'    => 'nullable|string|max:255',
            'purpose'        => 'nullable|string|max:1000',
            'agreed_to_terms'=> 'required|accepted',
        ]);

        $document = Document::findOrFail($request->document_id);

        $downloadRequest = DocumentDownloadRequest::create([
            'document_id'    => $document->id,
            'user_id'        => auth('sanctum')->id(),
            'name'           => $request->name,
            'email'          => $request->email,
            'institution'    => $request->institution,
            'purpose'        => $request->purpose,
            'agreed_to_terms'=> true,
            'status'         => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan download berhasil dikirim. Admin akan mengirimkan dokumen ke email Anda.',
            'data'    => $downloadRequest,
        ], 201);
    }

    public function index()
    {
        $requests = DocumentDownloadRequest::with(['document:id,title,file_path', 'user:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'document_id'  => $r->document_id,
                'title'        => $r->document?->title,
                'requester_name'  => $r->name,
                'requester_email' => $r->email,
                'institution'  => $r->institution,
                'purpose'      => $r->purpose,
                'status'       => $r->status,
                'sent_at'      => $r->sent_at?->format('d M Y H:i'),
                'created_at'   => $r->created_at->format('d M Y H:i'),
                'admin_notes'  => $r->admin_notes,
            ]);

        return response()->json(['success' => true, 'data' => $requests]);
    }

    public function send(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $downloadRequest = DocumentDownloadRequest::with('document')->findOrFail($id);
        $document = $downloadRequest->document;

        if (!$document || !$document->file_path) {
            return response()->json(['success' => false, 'message' => 'File dokumen tidak ditemukan'], 404);
        }

        $disk = Storage::disk('local')->exists($document->file_path) ? 'local' : 'public';
        $filePath = Storage::disk($disk)->path($document->file_path);

        if (!file_exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan di storage'], 404);
        }

        try {
            Mail::to($downloadRequest->email)
                ->send(new DocumentDownloadMail($downloadRequest, $filePath));

            $downloadRequest->update([
                'status'      => 'sent',
                'sent_at'     => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            Log::info("Document download sent", [
                'request_id'  => $downloadRequest->id,
                'email'       => $downloadRequest->email,
                'document_id' => $document->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Dokumen berhasil dikirim ke {$downloadRequest->email}",
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send document download email', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:1000']);

        $downloadRequest = DocumentDownloadRequest::findOrFail($id);
        $downloadRequest->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json(['success' => true, 'message' => 'Permintaan ditolak.']);
    }
}
