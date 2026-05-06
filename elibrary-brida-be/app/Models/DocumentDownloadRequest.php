<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDownloadRequest extends Model
{
    protected $fillable = [
        'document_id', 'user_id', 'name', 'email', 'institution',
        'purpose', 'agreed_to_terms', 'status', 'admin_notes', 'sent_at',
        'approval_token', 'owner_action_at', 'download_token',
        'download_token_expires_at', 'downloaded_at',
    ];

    protected $casts = [
        'agreed_to_terms' => 'boolean',
        'sent_at' => 'datetime',
        'owner_action_at' => 'datetime',
        'download_token_expires_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
