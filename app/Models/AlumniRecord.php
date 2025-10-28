<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumniRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumniRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumniRecord query()
 * @mixin \Eloquent
 */
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