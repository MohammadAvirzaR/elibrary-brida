<?php

namespace App\Mail;

use App\Models\DocumentDownloadRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DownloadRequestOwnerActionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DocumentDownloadRequest $downloadRequest,
        public string $approveUrl,
        public string $rejectUrl
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Download Dokumen: ' . ($this->downloadRequest->document?->title ?? 'Dokumen')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.download-request-owner-action'
        );
    }
}
