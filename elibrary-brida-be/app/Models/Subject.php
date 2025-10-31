<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_subject', 'subject_id', 'document_id');
    }

}
