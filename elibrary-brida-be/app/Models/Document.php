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
        'author',
        'publisher',
        'year_published',
        'type_id',
        'unit_id',
        'language',
        'email',
        'keywords',
        'subject',
        'file_path',
        'license_id',
        'status',
        'access_right',
        'abstract_id',
        'abstract_en',
        'translated_abstract',
        'funding_program',
        'supervisor',
        'advisor',
        'research_location',
        'latitude',
        'longitude',
        'embargo_until',
        'statement_agreed',
        'upload_date',
        'view_count',
        'download_count',
        'is_featured',
    ];

    protected $casts = [
        'embargo_until' => 'date',
        'statement_agreed' => 'boolean',
    ];

    public $timestamps = true;

    // ===================== relation ======================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function license()
    {
        return $this->belongsTo(License::class);
    }

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
