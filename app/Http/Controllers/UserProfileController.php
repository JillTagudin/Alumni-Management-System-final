<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alumni;  // Change to match your model name
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function edit()
    {
        return view('user.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:255'],
            'fullname' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:1', 'max:150'],
            'gender' => ['required', 'string', 'in:Male,Female'],
            'course' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'batch' => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'occupation' => ['required', 'string', 'max:255'],
        ]);

        // Update user profile
        User::where('id', $user->id)->update($validated);

        // First try to find existing alumni record
        $alumni = Alumni::where('StudentID', $validated['student_id'])->first();

        if ($alumni) {
            // Update existing record
            $alumni->update([
                'Fullname' => $validated['fullname'],
                'Age' => $validated['age'],
                'Gender' => $validated['gender'],
                'Course' => $validated['course'],
                'Section' => $validated['section'],
                'Batch' => $validated['batch'],
                'Contact' => $validated['contact'],
                'Address' => $validated['address'],
                'Emailaddress' => $validated['email'],
                'Occupation' => $validated['occupation'],
            ]);
        } else {
            // Create new record only if one doesn't exist
            Alumni::create([
                'StudentID' => $validated['student_id'],
                'Fullname' => $validated['fullname'],
                'Age' => $validated['age'],
                'Gender' => $validated['gender'],
                'Course' => $validated['course'],
                'Section' => $validated['section'],
                'Batch' => $validated['batch'],
                'Contact' => $validated['contact'],
                'Address' => $validated['address'],
                'Emailaddress' => $validated['email'],
                'Occupation' => $validated['occupation'],
            ]);
        }

        return redirect()->route('user.profile.edit')->with('status', 'profile-updated');
    }
}
