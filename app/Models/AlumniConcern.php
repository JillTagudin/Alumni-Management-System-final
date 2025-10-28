<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniConcern extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'admin_response',
        'responded_by',
        'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Constants for categories and statuses
    const CATEGORIES = [
        'general_inquiry' => 'General Inquiry',
        'technical_issue' => 'Technical Issue',
        'event_suggestion' => 'Event Suggestion',
        'membership_issue' => 'Membership Issue',
        'profile_update' => 'Profile Update Request',
        'complaint' => 'Complaint',
        'other' => 'Other'
    ];

    const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent'
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed'
    ];

    // Helper methods
    public function getCategoryLabelAttribute()
    {
        return self::CATEGORIES[$this->category] ?? 'Unknown';
    }

    public function getPriorityLabelAttribute()
    {
        return self::PRIORITIES[$this->priority] ?? 'Unknown';
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'text-green-600',
            'medium' => 'text-yellow-600',
            'high' => 'text-orange-600',
            'urgent' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'text-yellow-600 bg-yellow-100',
            'in_progress' => 'text-blue-600 bg-blue-100',
            'resolved' => 'text-green-600 bg-green-100',
            'closed' => 'text-gray-600 bg-gray-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
}