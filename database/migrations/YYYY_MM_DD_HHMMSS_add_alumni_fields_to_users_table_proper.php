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
            $table->string('alumni_id')->nullable()->after('email');
            $table->string('fullname')->nullable()->after('alumni_id');
            $table->integer('age')->nullable()->after('fullname');
            $table->string('gender')->nullable()->after('age');
            $table->string('course')->nullable()->after('gender');
            $table->string('section')->nullable()->after('course');
            $table->string('batch')->nullable()->after('section');
            $table->string('contact')->nullable()->after('batch');
            $table->string('address')->nullable()->after('contact');
            $table->string('occupation')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'alumni_id',
                'fullname',
                'age',
                'gender',
                'course',
                'section',
                'batch',
                'contact',
                'address',
                'occupation'
            ]);
        });
    }
};