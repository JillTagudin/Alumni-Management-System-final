<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_id')->nullable();
            $table->string('fullname')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('course')->nullable();
            $table->string('section')->nullable();
            $table->string('batch')->nullable();
            $table->string('contact')->nullable();
            $table->text('address')->nullable();
            $table->string('occupation')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'student_id', 'fullname', 'age', 'gender', 'course',
                'section', 'batch', 'contact', 'address', 'occupation'
            ]);
        });
    }
};