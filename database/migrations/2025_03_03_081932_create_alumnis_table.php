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
        Schema::create('alumnis', function (Blueprint $table) {
            $table->id();
            $table->string('StudentID')->unique(); // Add unique constraint
            $table->string('Fullname');
            $table->integer('Age');
            $table->string('Gender');
            $table->string('Course');
            $table->string('Section');
            $table->string('Batch');
            $table->string('Contact');
            $table->string('Address');
            $table->string('Emailaddress')->nullable();
            $table->string('Occupation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};
