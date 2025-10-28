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
        Schema::create('denied_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('role')->nullable();
            $table->string('student_number')->nullable();
            $table->text('approval_notes')->nullable();
            $table->unsignedBigInteger('denied_by');
            $table->timestamp('denied_at');
            $table->timestamp('original_created_at')->nullable();
            $table->json('original_user_data')->nullable(); // Store complete original user data
            $table->json('original_alumni_data')->nullable(); // Store complete original alumni data if exists
            $table->timestamps();

            $table->foreign('denied_by')->references('id')->on('users');
            $table->index(['email', 'denied_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denied_users');
    }
};