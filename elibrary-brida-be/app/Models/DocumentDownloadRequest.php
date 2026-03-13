<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDownloadRequest extends Model
{
    protected $fillable = [
        'document_id', 'user_id', 'name', 'email', 'institution',
        'purpose', 'agreed_to_terms', 'status', 'admin_notes', 'sent_at',
    ];

    protected $casts = [
        'agreed_to_terms' => 'boolean',
        'sent_at' => 'datetime',
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
