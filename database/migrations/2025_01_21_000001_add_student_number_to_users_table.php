<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_number')->nullable()->after('email');
        });
        
        // Generate unique student numbers for existing users
        $users = \App\Models\User::whereNull('student_number')->get();
        foreach ($users as $user) {
            $studentNumber = $this->generateUniqueStudentNumber();
            $user->update(['student_number' => $studentNumber]);
        }
        
        // Now add the unique constraint and make it non-nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_number')->nullable(false)->change();
            $table->unique('student_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['student_number']);
            $table->dropColumn('student_number');
        });
    }
    
    /**
     * Generate a unique student number
     */
    private function generateUniqueStudentNumber(): string
    {
        do {
            // Generate a student number in format: current year + 4 random digits
            $studentNumber = date('Y') . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        } while (\App\Models\User::where('student_number', $studentNumber)->exists());
        
        return $studentNumber;
    }
};