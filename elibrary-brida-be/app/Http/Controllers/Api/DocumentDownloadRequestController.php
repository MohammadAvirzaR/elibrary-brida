<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DocumentDownloadMail;
use App\Mail\DownloadApprovalLinkMail;
use App\Mail\DownloadRequestOwnerActionMail;
use App\Models\Document;
use App\Models\DocumentDownloadRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentDownloadRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'institution' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:1000',
            'agreed_to_terms' => 'required|accepted',
        ]);

        $document = Document::with('user')->findOrFail($request->document_id);

        $downloadRequest = DocumentDownloadRequest::create([
            'document_id' => $document->id,
            'user_id' => auth('sanctum')->id(),
            'name' => $request->input('name', auth('sanctum')->user()?->name ?? 'Requester'),
            'email' => $request->email,
            'institution' => $request->institution,
            'purpose' => $request->purpose,
            'agreed_to_terms' => true,
            'status' => 'pending',
            'approval_token' => Str::uuid()->toString(),
        ]);

        $this->notifyOwnerForAction($downloadRequest);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan download berhasil dikirim. Pemilik dokumen akan meninjau permintaan Anda.',
            'data' => $downloadRequest,
        ], 201);
    }

    public function requestByContent(Request $request, int $id)
    {
        $request->validate([
            'requester_email' => 'required|email',
            'name' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:1000',
        ]);

        $request->merge([
            'document_id' => $id,
            'email' => $request->input('requester_email'),
            'agreed_to_terms' => true,
        ]);

        return $this->store($request);
    }

    public function ownerPending(Request $request)
    {
        $user = $request->user();

        $requests = DocumentDownloadRequest::with(['document:id,title,user_id', 'user:id,full_name,email'])
            ->whereHas('document', fn ($q) => $q->where('user_id', $user->id))
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'content_id' => $r->document_id,
                'title' => $r->document?->title,
                'requester_email' => $r->email,
                'requester_name' => $r->name,
                'purpose' => $r->purpose,
                'status' => $r->status,
                'created_at' => $r->created_at,
            ]);

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    public function ownerApprove(Request $request, int $id)
    {
        $downloadRequest = DocumentDownloadRequest::with('document.user')->findOrFail($id);

        if ($downloadRequest->document?->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return $this->approveAndDispatchToRequester($downloadRequest);
    }

    public function ownerReject(Request $request, int $id)
    {
        $downloadRequest = DocumentDownloadRequest::with('document.user')->findOrFail($id);

        if ($downloadRequest->document?->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $downloadRequest->update([
            'status' => 'rejected',
            'owner_action_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Permintaan download ditolak.']);
    }

    public function approveViaToken(Request $request, int $id)
    {
        $token = $request->query('token');
        if (!$token) {
            return response('Invalid approval token.', 400);
        }

        $downloadRequest = DocumentDownloadRequest::with('document.user')
            ->where('id', $id)
            ->where('approval_token', $token)
            ->first();

        if (!$downloadRequest) {
            return response('Approval token invalid atau sudah kedaluwarsa.', 404);
        }

        $result = $this->approveAndDispatchToRequester($downloadRequest);
        if ($result->getStatusCode() >= 400) {
            return response('Gagal memproses persetujuan.', 500);
        }

        return response('Permintaan download disetujui. Link download telah dikirim ke requester.', 200);
    }

    public function rejectViaToken(Request $request, int $id)
    {
        $token = $request->query('token');
        if (!$token) {
            return response('Invalid reject token.', 400);
        }

        $downloadRequest = DocumentDownloadRequest::query()
            ->where('id', $id)
            ->where('approval_token', $token)
            ->first();

        if (!$downloadRequest) {
            return response('Reject token invalid atau sudah kedaluwarsa.', 404);
        }

        $downloadRequest->update([
            'status' => 'rejected',
            'owner_action_at' => now(),
        ]);

        return response('Permintaan download ditolak.', 200);
    }

    public function secureDownloadByToken(string $token)
    {
        $downloadRequest = DocumentDownloadRequest::with('document')
            ->where('download_token', $token)
            ->first();

        if (!$downloadRequest) {
            return response('Invalid token.', 404);
        }

        if ($downloadRequest->status !== 'sent') {
            return response('Token belum aktif.', 403);
        }

        if (!$downloadRequest->download_token_expires_at || Carbon::parse($downloadRequest->download_token_expires_at)->isPast()) {
            return response('Token kedaluwarsa.', 410);
        }

        if ($downloadRequest->downloaded_at) {
            return response('Link download sudah pernah digunakan.', 410);
        }

        $document = $downloadRequest->document;
        if (!$document || !$document->file_path) {
            return response('File dokumen tidak ditemukan.', 404);
        }

        $disk = Storage::disk('local')->exists($document->file_path) ? 'local' : 'public';
        $filePath = Storage::disk($disk)->path($document->file_path);

        if (!file_exists($filePath)) {
            return response('File dokumen tidak tersedia di server.', 404);
        }

        $downloadRequest->update([
            'downloaded_at' => now(),
        ]);

        Log::info('Secure download token used', [
            'request_id' => $downloadRequest->id,
            'document_id' => $downloadRequest->document_id,
            'requester_email' => $downloadRequest->email,
        ]);

        return response()->download($filePath, ($document->title ?: 'document') . '.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function index()
    {
        $requests = DocumentDownloadRequest::with(['document:id,title,file_path', 'user:id,full_name,email'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'document_id' => $r->document_id,
                'title' => $r->document?->title,
                'requester_name' => $r->name,
                'requester_email' => $r->email,
                'institution' => $r->institution,
                'purpose' => $r->purpose,
                'status' => $r->status,
                'sent_at' => $r->sent_at?->format('d M Y H:i'),
                'created_at' => $r->created_at->format('d M Y H:i'),
                'admin_notes' => $r->admin_notes,
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
                'status' => 'sent',
                'sent_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            Log::info("Document download sent", [
                'request_id' => $downloadRequest->id,
                'email' => $downloadRequest->email,
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
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json(['success' => true, 'message' => 'Permintaan ditolak.']);
    }

    private function notifyOwnerForAction(DocumentDownloadRequest $downloadRequest): void
    {
        $ownerEmail = $downloadRequest->document?->user?->email;
        if (!$ownerEmail) {
            Log::warning('Owner email not found for download request', [
                'request_id' => $downloadRequest->id,
                'document_id' => $downloadRequest->document_id,
            ]);
            return;
        }

        $baseUrl = rtrim(config('app.url'), '/');
        $approveUrl = "{$baseUrl}/download-request/{$downloadRequest->id}/approve?token={$downloadRequest->approval_token}";
        $rejectUrl = "{$baseUrl}/download-request/{$downloadRequest->id}/reject?token={$downloadRequest->approval_token}";

        Mail::to($ownerEmail)->send(
            new DownloadRequestOwnerActionMail($downloadRequest, $approveUrl, $rejectUrl)
        );
    }

    private function approveAndDispatchToRequester(DocumentDownloadRequest $downloadRequest)
    {
        $downloadToken = Str::uuid()->toString();
        $expiresAt = now()->addDay();

        $downloadRequest->update([
            'status' => 'sent',
            'owner_action_at' => now(),
            'sent_at' => now(),
            'download_token' => $downloadToken,
            'download_token_expires_at' => $expiresAt,
        ]);

        $downloadUrl = rtrim(config('app.url'), '/') . "/download/{$downloadToken}";

        Mail::to($downloadRequest->email)->send(
            new DownloadApprovalLinkMail($downloadRequest, $downloadUrl)
        );

        Log::info('Download request approved and link sent', [
            'request_id' => $downloadRequest->id,
            'document_id' => $downloadRequest->document_id,
            'requester_email' => $downloadRequest->email,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan disetujui. Link download terkirim ke requester.',
        ]);
    }
}
