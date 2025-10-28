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
        Schema::create('pending_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_user_id')->constrained('users')->onDelete('cascade');
            $table->string('change_type'); // 'role_assignment', 'user_creation', 'user_update', etc.
            $table->json('change_data'); // Store the actual change data
            $table->string('target_user_email')->nullable(); // Email of user being affected
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_changes');
    }
};