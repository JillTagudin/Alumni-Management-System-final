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
        // AlumniID is already nullable and has no unique constraint
        // No changes needed - the column is already in the desired state
        // This migration serves as a record that the constraint issue was investigated
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            // Check if the unique constraint exists before trying to drop it
            $indexExists = DB::select("SHOW INDEX FROM alumnis WHERE Key_name = 'alumnis_alumni_id_unique'");
            
            if (!empty($indexExists)) {
                $table->dropUnique(['AlumniID']);
            }
            
            // Make AlumniID not nullable again
            $table->string('AlumniID')->nullable(false)->change();
            
            // Restore the original unique constraint
            $table->unique('AlumniID');
        });
    }
};
