<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumniRecord extends Model
{
    protected $fillable = [
        'student_id',
        'fullname',
        'age',
        'gender',
        'course',
        'section',
        'batch',
        'contact',
        'address',
        'email',
        'occupation',
    ];
}