<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAuthor extends Model
{
    protected $fillable = [
        'document_id',
        'first_name',
        'last_name',
        'email',
        'institution',
        'university_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }
}
