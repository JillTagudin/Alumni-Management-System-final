<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JobOpportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company',
        'location',
        'job_type',
        'salary_range',
        'description',
        'requirements',
        'application_url',
        'contact_email',
        'contact_number',
        'application_deadline',
        'user_id',
        'status',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
        'application_deadline' => 'date'
    ];

    // Relationship with user who posted the job
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if job opportunity is expired
    public function isExpired()
    {
        return $this->application_deadline && $this->application_deadline->isPast();
    }

    // Get formatted salary range
    public function getFormattedSalaryAttribute()
    {
        return $this->salary_range ?: 'Not specified';
    }

    // Get job type badge color
    public function getJobTypeBadgeColorAttribute()
    {
        $colors = [
            'full-time' => 'bg-green-100 text-green-800',
            'part-time' => 'bg-blue-100 text-blue-800',
            'contract' => 'bg-yellow-100 text-yellow-800',
            'internship' => 'bg-purple-100 text-purple-800',
            'remote' => 'bg-indigo-100 text-indigo-800'
        ];

        return $colors[$this->job_type] ?? 'bg-gray-100 text-gray-800';
    }
}