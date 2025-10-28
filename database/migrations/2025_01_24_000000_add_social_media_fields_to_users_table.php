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
            $table->string('facebook_profile')->nullable()->after('profile_picture');
            $table->string('linkedin_profile')->nullable()->after('facebook_profile');
            $table->string('twitter_profile')->nullable()->after('linkedin_profile');
            $table->string('instagram_profile')->nullable()->after('twitter_profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_profile',
                'linkedin_profile', 
                'twitter_profile',
                'instagram_profile'
            ]);
        });
    }
};