<?php

namespace App\Mail;

use App\Models\DocumentDownloadRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentDownloadMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DocumentDownloadRequest $downloadRequest,
        public string $filePath
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dokumen yang Anda Minta — ' . $this->downloadRequest->document?->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.document-download',
        );
    }

    public function attachments(): array
    {
        $fileName = $this->downloadRequest->document?->title . '.pdf';
        return [
            Attachment::fromPath($this->filePath)->as($fileName)->withMime('application/pdf'),
        ];
    }
}
