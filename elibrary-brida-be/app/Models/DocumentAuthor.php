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
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
