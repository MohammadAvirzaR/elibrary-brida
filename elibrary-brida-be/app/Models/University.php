<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $table = 'university';

    protected $fillable = [
        'university_name',
    ];

    public $timestamps = true;

    // Relations
    public function documentAuthors()
    {
        return $this->hasMany(DocumentAuthor::class);
    }
}
