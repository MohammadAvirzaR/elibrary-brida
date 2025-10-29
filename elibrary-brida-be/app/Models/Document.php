<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'documents'; // ganti sesuai nama tabel di migrasi kamu

    // Kolom yang bisa diisi
    protected $fillable = [
        'user_id',
        'title',
        'author',
        'year_published',
        'type_id',
        'unit_id',
        'language',
        'email',
        'keywords',
        'abstract',
        'file_path',
        'upload_date',
        'view_count',
        'download_count',
        'is_featured',
        'license_id',
        'funding_program',
        'supervisor',
        'status',
        'research_location',
        'latitude',
        'longitude',
        'access_right',
    ];

    // Kalau tabel tidak punya created_at & updated_at
    public $timestamps = false;

    // 🔗 Relasi ke tabel lain
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

    // 🔍 Scope untuk search agar controller tetap bersih
    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            return $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%")
                    ->orWhere('keywords', 'like', "%{$keyword}%")
                    ->orWhere('abstract', 'like', "%{$keyword}%")
                    ->orWhere('year_published', 'like', "%{$keyword}%");
            });
        }

        return $query;
    }
}
