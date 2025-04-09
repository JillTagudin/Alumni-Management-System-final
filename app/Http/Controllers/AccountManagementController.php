<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AccountManagementController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('AccountManagement.index', ['users' => $users]);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'is_admin' => 'required|boolean'
        ]);

        $user->update([
            'is_admin' => $request->is_admin
        ]);

        return redirect()->back()->with('success', 'User role updated successfully');
    }
}
