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
        Schema::create('job_opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('company');
            $table->string('location');
            $table->string('job_type')->default('Full-time'); // Full-time, Part-time, Contract, Internship
            $table->string('category')->default('General'); // IT, Engineering, Business, etc.
            $table->longText('description');
            $table->longText('requirements')->nullable();
            $table->string('salary_range')->nullable();
            $table->string('application_url')->nullable();
            $table->string('contact_email')->nullable();
            $table->date('application_deadline')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who posted it
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->json('attachments')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['category', 'status']);
            $table->index(['application_deadline', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_opportunities');
    }
};