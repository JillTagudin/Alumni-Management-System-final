<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MembershipController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Secured API using sanctum middleware
Route::middleware('auth:sanctum')->group(function () {
    // Payment Records API
    Route::get('/alumni/payments', [MembershipController::class, 'apiPaymentRecords']);
    Route::get('/alumni/payments/stats', [MembershipController::class, 'apiPaymentStats']);
    Route::get('/alumni/payments/{id}', [MembershipController::class, 'apiPaymentRecord']);
    
    // General Alumni API
    Route::get('/alumni', [MembershipController::class, 'apiIndex']);
    Route::put('/alumni/{id}/membership', [MembershipController::class, 'updateMembership']);
    
    // Dashboard Chart Data API
    Route::get('/dashboard/alumni-chart-data', [MembershipController::class, 'getAlumniChartData']);
    
    // Chatbot API
    Route::get('/chatbot/common-queries', [MembershipController::class, 'getCommonQueries']);
});

// Unsecured API (if needed for public access)
// Route::get('/alumni/public', [MembershipController::class, 'apiIndex']);

// Validation API endpoints
Route::post('/check-student-number', [App\Http\Controllers\Api\ValidationController::class, 'checkStudentNumber']);
