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
            // First, drop the existing unique constraint
            $table->dropUnique(['student_number']);
            
            // Make student_number nullable
            $table->string('student_number')->nullable()->change();
            
            // Add unique constraint only for non-null values
            $table->unique('student_number', 'users_student_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('users_student_number_unique');
            
            // Make student_number non-nullable again
            $table->string('student_number')->nullable(false)->change();
            
            // Re-add the original unique constraint
            $table->unique('student_number');
        });
    }
};
