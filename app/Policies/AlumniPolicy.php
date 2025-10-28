<?php

namespace App\Policies;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlumniPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any alumni records.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Staff', 'SuperAdmin']);
    }

    /**
     * Determine whether the user can view the alumni record.
     */
    public function view(User $user, Alumni $alumni): bool
    {
        // Admin, Staff, and SuperAdmin can view any alumni record
        if (in_array($user->role, ['Admin', 'Staff', 'SuperAdmin'])) {
            return true;
        }

        // Alumni users can only view their own record
        return $user->role === 'Alumni' && $user->email === $alumni->Emailaddress;
    }

    /**
     * Determine whether the user can create alumni records.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Staff', 'SuperAdmin']);
    }

    /**
     * Determine whether the user can update the alumni record.
     */
    public function update(User $user, Alumni $alumni): bool
    {
        return in_array($user->role, ['Admin', 'Staff', 'SuperAdmin']) || $user->email === $alumni->Emailaddress;
    }

    /**
     * Determine whether the user can delete the alumni record.
     */
    public function delete(User $user, Alumni $alumni): bool
    {
        // Only Admin, Staff, and SuperAdmin can delete alumni records
        return in_array($user->role, ['Admin', 'Staff', 'SuperAdmin']);
    }

    /**
     * Determine whether the user can restore the alumni record.
     */
    public function restore(User $user, Alumni $alumni): bool
    {
        return in_array($user->role, ['Admin', 'SuperAdmin']);
    }

    /**
     * Determine whether the user can permanently delete the alumni record.
     */
    public function forceDelete(User $user, Alumni $alumni): bool
    {
        return in_array($user->role, ['Admin', 'SuperAdmin']);
    }
}