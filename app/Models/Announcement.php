<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'content',
        'user_id',
        'status',
        'attachments',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'attachments' => 'array'
    ];

    // Add the user relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Add this method to the Announcement model
    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }
    
    public function readByUsers()
    {
        return $this->belongsToMany(User::class, 'user_announcement_reads')
                    ->withTimestamps();
    }
}
