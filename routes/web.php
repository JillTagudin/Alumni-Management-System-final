<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\Auth\UserRegisterController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\AccountManagementController; // Add this line
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/announcement', function () {
        return view('announcement');
    })->name('announcement');

    Route::get('/Module 2', function () {
        return view('dashboard');
    })->name('Module 2');

    Route::get('/AccountManagement.index', function () {
        return view('AccountManagement.index');
    })->name('AccountManagement.index');

    // Alumni Management Routes
    Route::get('/Alumni', [AlumniController::class, 'index'])->name('Alumni.index');
    Route::get('/Alumni/create', [AlumniController::class, 'create'])->name('Alumni.create');
    Route::post('/Alumni', [AlumniController::class, 'store'])->name('Alumni.store');
    Route::get('/Alumni/{Alumni}', [AlumniController::class, 'edit'])->name('Alumni.edit');
    Route::put('/Alumni/{Alumni}', [AlumniController::class, 'update'])->name('Alumni.update');
    Route::delete('/Alumni/{Alumni}', [AlumniController::class, 'destroy'])->name('Alumni.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// User Routes
Route::middleware('auth')->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/announcement', function () {
        return view('user.announcement');
    })->name('user.announcement');
    
    Route::get('/user/profile', [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::patch('/user/profile', [UserProfileController::class, 'update'])->name('user.profile.update');
    Route::put('/user/profile/password', [UserProfileController::class, 'updatePassword'])->name('user.password.update');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/user-register', [UserRegisterController::class, 'create'])->name('user.register');
    Route::post('/user-register', [UserRegisterController::class, 'store']);
    Route::post('/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});


Route::middleware(['auth', 'verified'])->group(function () {
    
    // In your admin routes group
    Route::get('/AccountManagement', [AccountManagementController::class, 'index'])->name('AccountManagement.index');
    Route::patch('/AccountManagement/{user}/role', [AccountManagementController::class, 'updateRole'])->name('AccountManagement.updateRole');
});





require __DIR__.'/auth.php';




