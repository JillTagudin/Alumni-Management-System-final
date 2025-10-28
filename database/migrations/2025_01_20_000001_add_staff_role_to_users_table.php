<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, ensure all existing NULL values are set to 'Alumni'
        DB::statement("UPDATE users SET role = 'Alumni' WHERE role IS NULL");
        
        // Drop and recreate the column with the new enum values (SQLite compatible)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Alumni', 'Staff'])->default('Alumni')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, convert any 'Staff' users to 'Alumni' before removing 'Staff' from enum
        DB::statement("UPDATE users SET role = 'Alumni' WHERE role = 'Staff'");
        
        // Drop and recreate the column with the original enum values
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Alumni'])->default('Alumni')->nullable()->after('email');
        });
    }
};