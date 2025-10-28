<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PendingChange;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountManagementController extends Controller
{
    public function index()
    {
        // Get fresh data from database, avoid any model caching
        $users = User::orderBy('name')->get()->fresh();
        return view('AccountManagement.index', ['users' => $users]);
    }

    public function updateRole(Request $request, User $user)
    {
        $currentUser = auth()->user();

        // Block Staff from updating any accounts (no pending change creation)
        if ($currentUser && $currentUser->role === 'Staff') {
            if ($request->ajax()) {
                return response()->json(['error' => 'You are not authorized to update accounts.'], 403);
            }
            return redirect()->route('AccountManagement.index')
                ->with('error', 'You are not authorized to update accounts.');
        }

        // Only Admin and SuperAdmin can proceed
        if ($currentUser && $currentUser->role === 'SuperAdmin') {
            $allowedRoles = ['Alumni', 'Staff', 'HR', 'Admin', 'SuperAdmin'];
        } elseif ($currentUser && $currentUser->role === 'Admin') {
            $allowedRoles = ['Alumni', 'Staff', 'HR', 'Admin']; // Added HR role here
        } else {
            // Non-admins cannot update accounts
            if ($request->ajax()) {
                return response()->json(['error' => 'You are not authorized to update accounts.'], 403);
            }
            return redirect()->route('AccountManagement.index')
                ->with('error', 'You are not authorized to update accounts.');
        }

        $request->validate([
            'role' => 'required|in:' . implode(',', $allowedRoles),
        ]);

        $oldRole = $user->role;
        $newRole = $request->role;

        // Use database transaction for data integrity
        DB::beginTransaction();
        
        try {
            // Clear any model cache
            $user->refresh();
            
            // Update the user's role using direct database query to avoid any model issues
            $updated = DB::table('users')
                ->where('id', $user->id)
                ->update(['role' => $newRole, 'updated_at' => now()]);
            
            if (!$updated) {
                throw new \Exception("Database update failed - no rows affected");
            }

            // Refresh the model to get the latest data from database
            $user->refresh();

            // Verify the role was actually updated with a fresh query
            $actualRole = DB::table('users')->where('id', $user->id)->value('role');
            if ($actualRole !== $newRole) {
                throw new \Exception("Role update verification failed. Expected: {$newRole}, Actual: {$actualRole}");
            }

            // Log the activity
            ActivityLog::log(
                'role_change_requested',
                "User role changed from {$oldRole} to {$newRole}",
                auth()->id(),
                [
                    'target_user_id' => $user->id,
                    'target_user_email' => $user->email,
                    'old_role' => $oldRole,
                    'new_role' => $newRole,
                    'verification_role' => $actualRole
                ]
            );

            DB::commit();

            Log::info("Role update successful", [
                'user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $newRole,
                'verified_role' => $actualRole,
                'updated_by' => auth()->id()
            ]);

            // Clear any application cache that might be storing user data
            if (function_exists('cache')) {
                cache()->forget("user.{$user->id}");
                cache()->forget("users.all");
            }

            $successMessage = "User role updated successfully from {$oldRole} to {$newRole}.";

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'redirect' => route('AccountManagement.index')
                ]);
            }

            return redirect()->route('AccountManagement.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error("Role update failed", [
                'user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $newRole,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id()
            ]);

            $errorMessage = 'Failed to update user role: ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json(['error' => $errorMessage], 500);
            }

            return redirect()->route('AccountManagement.index')
                ->with('error', $errorMessage);
        }
    }

    /**
     * SuperAdmin-specific user management methods
     */
    public function superAdminIndex()
    {
        // Only SuperAdmin can access this
        if (auth()->user()->role !== 'SuperAdmin') {
            abort(403, 'Unauthorized access. SuperAdmin privileges required.');
        }

        $users = User::orderBy('name')->get()->fresh();
        return view('superadmin.users.index', ['users' => $users]);
    }

    public function superAdminUpdateRole(Request $request, User $user)
    {
        // Only SuperAdmin can access this
        if (auth()->user()->role !== 'SuperAdmin') {
            abort(403, 'Unauthorized access. SuperAdmin privileges required.');
        }

        $allowedRoles = ['Alumni', 'Staff', 'HR', 'Admin', 'SuperAdmin'];
        
        $request->validate([
            'role' => 'required|in:' . implode(',', $allowedRoles),
        ]);

        $oldRole = $user->role;
        $newRole = $request->role;

        DB::beginTransaction();
        
        try {
            $user->refresh();
            
            $updated = DB::table('users')
                ->where('id', $user->id)
                ->update(['role' => $newRole, 'updated_at' => now()]);
            
            if (!$updated) {
                throw new \Exception("Database update failed - no rows affected");
            }

            $user->refresh();

            $actualRole = DB::table('users')->where('id', $user->id)->value('role');
            if ($actualRole !== $newRole) {
                throw new \Exception("Role update verification failed. Expected: {$newRole}, Actual: {$actualRole}");
            }

            ActivityLog::log(
                'superadmin_role_change',
                "SuperAdmin changed user role from {$oldRole} to {$newRole}",
                auth()->id(),
                [
                    'target_user_id' => $user->id,
                    'target_user_email' => $user->email,
                    'old_role' => $oldRole,
                    'new_role' => $newRole,
                    'verification_role' => $actualRole
                ]
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "User role updated successfully from {$oldRole} to {$newRole}",
                    'new_role' => $actualRole
                ]);
            }

            return redirect()->route('superadmin.users')
                ->with('success', "User role updated successfully from {$oldRole} to {$newRole}");

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('SuperAdmin role update failed', [
                'user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $newRole,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id()
            ]);

            $errorMessage = 'Failed to update user role: ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json(['error' => $errorMessage], 500);
            }

            return redirect()->route('superadmin.users')
                ->with('error', $errorMessage);
        }
    }

    public function superAdminDeleteUser(Request $request, User $user)
    {
        // Only SuperAdmin can access this
        if (auth()->user()->role !== 'SuperAdmin') {
            abort(403, 'Unauthorized access. SuperAdmin privileges required.');
        }

        // Prevent SuperAdmin from deleting themselves
        if ($user->id === auth()->id()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'You cannot delete your own account.'], 400);
            }
            return redirect()->route('superadmin.users')
                ->with('error', 'You cannot delete your own account.');
        }

        DB::beginTransaction();
        
        try {
            $userName = $user->name;
            $userEmail = $user->email;
            $userRole = $user->role;

            // Log the deletion before actually deleting
            ActivityLog::log(
                'superadmin_user_deletion',
                "SuperAdmin deleted user: {$userName} ({$userEmail})",
                auth()->id(),
                [
                    'deleted_user_id' => $user->id,
                    'deleted_user_email' => $userEmail,
                    'deleted_user_role' => $userRole,
                    'deleted_user_name' => $userName
                ]
            );

            $user->delete();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "User {$userName} deleted successfully"
                ]);
            }

            return redirect()->route('superadmin.users')
                ->with('success', "User {$userName} deleted successfully");

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('SuperAdmin user deletion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'deleted_by' => auth()->id()
            ]);

            $errorMessage = 'Failed to delete user: ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json(['error' => $errorMessage], 500);
            }

            return redirect()->route('superadmin.users')
                ->with('error', $errorMessage);
        }
    }
}
