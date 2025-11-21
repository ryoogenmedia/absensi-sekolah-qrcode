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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('subject_study_id')->nullable();
            $table->string('name');
            $table->string('sex')->nullable();
            $table->string('nip')->unique()->nullable();
            $table->string('nuptk')->nullable();
            $table->string('phone')->nullable();
            $table->string('religion')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->date('date_joined')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
