<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, set any NULL roles to 'Alumni' to avoid constraint violations
        DB::table('users')->whereNull('role')->update(['role' => 'Alumni']);
        
        // Modify the enum to include SuperAdmin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Alumni', 'Staff', 'Admin', 'SuperAdmin') NOT NULL DEFAULT 'Alumni'");
    }

    public function down(): void
    {
        // Convert any SuperAdmin roles to Admin before removing the enum value
        DB::table('users')->where('role', 'SuperAdmin')->update(['role' => 'Admin']);
        
        // Revert the enum to exclude SuperAdmin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Alumni', 'Staff', 'Admin') NOT NULL DEFAULT 'Alumni'");
    }
};