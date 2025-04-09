<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    protected $table = 'alumnis';
    
    protected $fillable = [
        'StudentID',
        'Fullname',
        'Age',
        'Gender',
        'Course',
        'Section',
        'Batch',
        'Contact',
        'Address',
        'Emailaddress',
        'Occupation'
    ];
}
