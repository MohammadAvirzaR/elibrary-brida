<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSupervisor extends Model
{
    protected $fillable = [
        'document_id',
        'name',
        'email',
        'institution',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
