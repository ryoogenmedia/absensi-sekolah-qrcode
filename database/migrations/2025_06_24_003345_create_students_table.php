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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('class_room_id');
            $table->boolean('in_school')->default(false)->nullable();
            $table->string('full_name')->nullable();
            $table->string('call_name')->nullable();
            $table->string('sex');
            $table->string('nis')->unique();
            $table->string('phone')->nullable();
            $table->string('religion')->nullable();
            $table->string('origin_school')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->year('admission_year')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('father_job')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
