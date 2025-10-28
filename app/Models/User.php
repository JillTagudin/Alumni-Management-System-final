<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $alumni_id
 * @property string|null $student_number
 * @property string|null $fullname
 * @property int|null $age
 * @property string|null $gender
 * @property string|null $course
 * @property string|null $section
 * @property string|null $batch
 * @property string|null $contact
 * @property string|null $address
 * @property string|null $occupation
 * @property string $timezone
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCourse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAlumniId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStudentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{

    









    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'alumni_id',
        'student_number',
        'fullname',
        'age',
        'gender',
        'course',
        'section',
        'batch',
        'contact',
        'address',
        'occupation',
        'company',
        'profile_picture',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'two_factor_code',
        'two_factor_code_expires_at',
        'facebook_profile',
        'linkedin_profile',
        'twitter_profile',
        'instagram_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
        'two_factor_code_expires_at' => 'datetime',
        'approved_at' => 'datetime',
    ];







    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    /**
     * Get the activity logs for the user.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Check if user is alumni
     */
    public function isAlumni(): bool
    {
        return $this->role === 'Alumni';
    }

    /**
     * Check if user is a staff member
     */
    public function isStaff(): bool
    {
        return $this->role === 'Staff';
    }

    /**
     * Check if the user is a SuperAdmin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'SuperAdmin';
    }

    /**
     * Check if the user has admin privileges (Staff, Admin or SuperAdmin).
     */
    public function hasAdminPrivileges(): bool
    {
        return in_array($this->role, ['Staff', 'Admin', 'SuperAdmin']);
    }

    /**
     * Check if the user has super admin privileges.
     */
    public function hasSuperAdminPrivileges(): bool
    {
        return $this->role === 'SuperAdmin';
    }

    /**
     * Check if user is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if user is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if user is denied
     */
    public function isDenied(): bool
    {
        return $this->approval_status === 'denied';
    }

    /**
     * Approve the user
     */
    public function approve($approvedBy = null, $notes = null): void
    {
        $this->update([
            'approval_status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Deny the user
     */
    public function deny($deniedBy = null, $notes = null): void
    {
        $this->update([
            'approval_status' => 'denied',
            'approved_by' => $deniedBy,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Get the user who approved/denied this user
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate and send 2FA code via email
     */
    public function generateTwoFactorCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'two_factor_code' => $code,
            'two_factor_code_expires_at' => now()->addMinutes(10),
        ]);
        
        return $code;
    }

    /**
     * Verify 2FA code
     */
    public function verifyTwoFactorCode(string $code): bool
    {
        if (!$this->two_factor_code || !$this->two_factor_code_expires_at) {
            return false;
        }
        
        if (now()->isAfter($this->two_factor_code_expires_at)) {
            return false;
        }
        
        return $this->two_factor_code === $code;
    }

    /**
     * Clear 2FA code after successful verification
     */
    public function clearTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code' => null,
            'two_factor_code_expires_at' => null,
        ]);
    }

    /**
     * Get the profile picture URL with fallback to default avatar
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_picture)) {
            return asset('storage/' . $this->profile_picture);
        }
        
        // Generate default avatar using initials
        $initials = $this->getInitials();
        return "https://ui-avatars.com/api/?name={$initials}&color=7F9CF5&background=EBF4FF&size=128";
    }

    /**
     * Get user initials for default avatar
     */
    public function getInitials(): string
    {
        $name = $this->fullname ?: $this->name;
        $words = explode(' ', trim($name));
        
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        return strtoupper(substr($name, 0, 2));
    }

    /**
     * Get initials attribute accessor
     */
    public function getInitialsAttribute(): string
    {
        return $this->getInitials();
    }
    
    /**
     * Get the alumni record associated with this user
     */
    public function alumni()
    {
        return $this->hasOne(Alumni::class, 'user_id');
    }
    
    /**
     * Legacy alumni relationship using email (kept for backward compatibility)
     */
    public function alumniByEmail()
    {
        return $this->hasOne(Alumni::class, 'Emailaddress', 'email');
    }



    // Add this method to the existing User model
    public function isHR()
    {
        return $this->role === 'HR';
    }
    
    public function hasHRPrivileges()
    {
        return in_array($this->role, ['HR', 'Admin', 'SuperAdmin']);
    }

    public function announcementReads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }
    
    public function readAnnouncements()
    {
        return $this->belongsToMany(Announcement::class, 'user_announcement_reads')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    /**
     * Check if user has any social media profiles linked
     */
    public function hasSocialMediaProfiles(): bool
    {
        return !empty($this->facebook_profile) || 
               !empty($this->linkedin_profile) || 
               !empty($this->twitter_profile) || 
               !empty($this->instagram_profile);
    }

    /**
     * Get all social media profiles as an array
     */
    public function getSocialMediaProfiles(): array
    {
        $profiles = [];
        
        if ($this->facebook_profile) {
            $profiles['facebook'] = [
                'name' => 'Facebook',
                'url' => $this->formatSocialMediaUrl($this->facebook_profile, 'https://facebook.com/'),
                'icon' => 'fab fa-facebook-f',
                'color' => '#1877F2'
            ];
        }
        
        if ($this->linkedin_profile) {
            $profiles['linkedin'] = [
                'name' => 'LinkedIn',
                'url' => $this->formatSocialMediaUrl($this->linkedin_profile, 'https://linkedin.com/in/'),
                'icon' => 'fab fa-linkedin-in',
                'color' => '#0A66C2'
            ];
        }
        
        if ($this->twitter_profile) {
            $profiles['twitter'] = [
                'name' => 'Twitter',
                'url' => $this->formatSocialMediaUrl($this->twitter_profile, 'https://twitter.com/'),
                'icon' => 'fab fa-twitter',
                'color' => '#1DA1F2'
            ];
        }
        
        if ($this->instagram_profile) {
            $profiles['instagram'] = [
                'name' => 'Instagram',
                'url' => $this->formatSocialMediaUrl($this->instagram_profile, 'https://instagram.com/'),
                'icon' => 'fab fa-instagram',
                'color' => '#E4405F'
            ];
        }
        
        return $profiles;
    }
    
    /**
     * Format social media URL to handle both full URLs and usernames
     */
    private function formatSocialMediaUrl(string $profile, string $baseUrl): string
    {
        // If the profile already starts with http:// or https://, return as is
        if (preg_match('/^https?:\/\//i', $profile)) {
            return $profile;
        }
        
        // If it starts with www., add https://
        if (str_starts_with(strtolower($profile), 'www.')) {
            return 'https://' . $profile;
        }
        
        // Otherwise, treat as username and prepend base URL
        return $baseUrl . ltrim($profile, '/');
    }
}
