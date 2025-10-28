<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_user_id',
        'change_type',
        'change_data',
        'target_user_email',
        'status',
        'reviewed_by',
        'review_notes',
        'reviewed_at'
    ];

    protected $casts = [
        'change_data' => 'array',
        'reviewed_at' => 'datetime'
    ];

    /**
     * Get the staff user who requested the change
     */
    public function staffUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    /**
     * Get the admin user who reviewed the change
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope for pending changes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved changes
     */
    public function scopeApproved($query)
    {
        return $this->where('status', 'approved');
    }

    /**
     * Scope for denied changes
     */
    public function scopeDenied($query)
    {
        return $query->where('status', 'denied');
    }

    /**
     * Get formatted change description
     */
    public function getChangeDescriptionAttribute()
    {
        switch ($this->change_type) {
            case 'role_assignment':
                return "Role change to {$this->change_data['new_role']} for {$this->target_user_email}";
            case 'user_creation':
                return "Create new user: {$this->change_data['email']} with role {$this->change_data['role']}";
            case 'user_update':
                return "Update user: {$this->target_user_email}";
            case 'job_opportunity_creation':
                return "Create job opportunity: {$this->change_data['title']} at {$this->change_data['company']}";
            case 'alumni_creation':
                return "Create alumni record for: {$this->change_data['Fullname']}";
            case 'alumni_update':
                return "Update alumni record for: {$this->change_data['Fullname']}";
            default:
                return "Unknown change type: {$this->change_type}";
        }
    }
}