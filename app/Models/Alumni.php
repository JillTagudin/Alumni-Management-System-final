<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    protected $table = 'alumnis';
    
    protected $fillable = [
        'user_id',
        'AlumniID', // Changed from StudentID to AlumniID
        'student_number',
        'Fullname',
        'Age',
        'Gender',
        'Course',
        'Section',
        'Batch',
        'Contact',
        'Address',
        'Emailaddress',
        'Occupation',
        'Company',
        'membership_status',
        'membership_type',
        'payment_amount'
    ];

    // Enable timestamps since migration creates created_at and updated_at columns
    public $timestamps = true;
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'membership_status' => 'Pending',
        'membership_type' => 'Annual',
    ];
    
    /**
     * Get the user associated with this alumni record
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Legacy relationship using email (kept for backward compatibility)
     */
    public function userByEmail()
    {
        return $this->belongsTo(User::class, 'Emailaddress', 'email');
    }
}