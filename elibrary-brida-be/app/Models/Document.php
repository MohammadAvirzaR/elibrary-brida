<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'user_id',
        'title',
        'year_published',
        'type_id',
        'unit_id',
        'language',
        'email',
        'keywords',
        'file_path',
        'license_id',
        'status',
        'access_right',
        'abstract_id',
        'abstract_en',
        'funding_program',
        'research_location',
        'latitude',
        'longitude',
        'embargo_until',
        'statement_agreed',
        'upload_date',
    ];

    protected $casts = [
        'embargo_until' => 'date',
        'statement_agreed' => 'boolean',
    ];

    // ===================== RELASI ======================

    public function authors()
    {
        return $this->hasMany(DocumentAuthor::class);
    }

    public function supervisors()
    {
        return $this->hasMany(DocumentSupervisor::class);
    }

    public function attachments()
    {
        return $this->hasMany(DocumentAttachment::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'document_subject');
    }
}
