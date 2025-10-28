<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Modify the role enum to include HR
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Alumni', 'Staff', 'HR', 'Admin', 'SuperAdmin') NOT NULL DEFAULT 'Alumni'");
    }

    public function down()
    {
        // Convert any HR users back to Staff before removing the enum value
        DB::table('users')->where('role', 'HR')->update(['role' => 'Staff']);
        
        // Revert the role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Alumni', 'Staff', 'Admin', 'SuperAdmin') NOT NULL DEFAULT 'Alumni'");
    }
};