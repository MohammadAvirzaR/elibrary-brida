<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'comment',
        'status_review',
        'review_date',
    ];

    public $timestamps = false; // We use review_date instead of created_at/updated_at

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
