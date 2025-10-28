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
        Schema::table('alumnis', function (Blueprint $table) {
            $table->enum('membership_status', ['Active', 'Inactive', 'Pending'])->default('Pending');
            $table->enum('membership_type', ['Annual', 'Lifetime'])->default('Annual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            $table->dropColumn(['membership_status', 'membership_type']);
        });
    }
};
