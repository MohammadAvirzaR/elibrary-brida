<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributorRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'message',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Relasi: ContributorRequest -> User
     * Satu request milik satu user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: ContributorRequest -> User (reviewed_by)
     * Admin yang review request
     */
    public function reviewedByUser()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
