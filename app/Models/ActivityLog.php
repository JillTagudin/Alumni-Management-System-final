<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string $description
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array<array-key, mixed>|null $properties
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserId($value)
 * @mixin \Eloquent
 */
class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the description attribute and ensure it's always a string.
     * This prevents htmlspecialchars errors when descriptions are stored as arrays.
     */
    public function getDescriptionAttribute($value)
    {
        // If the value is already a string, return it as-is
        if (is_string($value)) {
            return $value;
        }
        
        // If it's an array, convert it to a readable string
        if (is_array($value)) {
            return implode(', ', array_filter($value, 'is_string'));
        }
        
        // If it's null or other type, convert to string
        return (string) $value;
    }

    /**
     * Get the user that performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity.
     */
    public static function log(string $action, string $description, ?int $userId = null, array $properties = []): self
    {
        // If userId is explicitly passed as null, keep it null
        // Otherwise, use the authenticated user's ID
        $finalUserId = func_num_args() >= 3 ? $userId : auth()->id();
        
        return self::create([
            'user_id' => $finalUserId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'properties' => $properties,
        ]);
    }

    // Add new methods for analytics
    public static function getActionFrequency($days = 30)
    {
        return self::select('action', DB::raw('COUNT(*) as count'))
                  ->where('created_at', '>=', now()->subDays($days))
                  ->groupBy('action')
                  ->orderBy('count', 'desc')
                  ->get();
    }

    public static function getUserActivityScore($userId, $days = 30)
    {
        return self::where('user_id', $userId)
                  ->where('created_at', '>=', now()->subDays($days))
                  ->count();
    }

    public static function getHourlyActivity($days = 7)
    {
        return self::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays($days))
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();
    }
}